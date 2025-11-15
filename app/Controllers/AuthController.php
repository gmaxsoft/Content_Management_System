<?php

namespace App\Controllers;

use Core\View;
use Core\Messages as Message;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Auth as DB;
use App\Models\ServicePassword;

class AuthController
{

    public function getPasswdHash()
    {
        $passwdHash = ServicePassword::find(1)?->passwd_hash ?? 'false';
        // lub z Query Builderem:
        // $passwdHash = DB::table('service_passwd')->where('passwd_id', 1)->value('passwd_hash') ?? 'false';

        return $passwdHash;
    }

    public function login()
    {
        # Get all input values
        # $post = input()->all();

        $expire_month = 6 * 30 * 24 * 3600; // 6 months
        $expire_day = 86400 * 30;  // 30 days - 86400 = 1 day

        # Only match specific keys
        $post = input()->all([
            'email',
            'password'
        ]);

        $email = $post['email'];
        $password = $post['password'];

        if (empty($email)) {
            Message::addMessage('Proszę wpisać e-mail!', Message::WARNING);
            View::redirect('/');
        } else if (empty($password)) {
            Message::addMessage('Proszę wprowadzić hasło!', Message::WARNING);
            View::redirect('/');
        } else {

            $data = DB::where('user_email', '=', $email, 'and')->where('user_active', '=', '1')->first();

            if ($data) {

                $payload = array(
                    'iat'        =>    time(),
                    'nbf'        =>    time(),
                    'exp'        =>    time() + $expire_day,
                    'data'    => array(
                        'user_id'    =>    $data['user_id'],
                        'user_first_name'    =>    $data['user_first_name'],
                        'user_last_name'    =>    $data['user_last_name']
                    )
                );

                if (password_verify($password, $data['user_password'])) {

                    $key = $_ENV['SECRET_KEY'];
                    $token = JWT::encode($payload, $key, 'HS256');

                    $_SESSION['userinfo'] = [
                        'user_id' => $data['user_id'],
                        'user_first_name' => $data['user_first_name'],
                        'user_last_name' => $data['user_last_name'],
                        'user_active' => $data['user_active'],
                        'user_logged_in' => true,
                        'service_login' => false,
                    ];

                    if (isset($post['remember']))
                        setcookie("token", $token, time() + $expire_month, "/", "", true, true);
                    else
                        setcookie("token", $token, time() + $expire_day, "/", "", true, true);

                    View::redirect('/dashboard');
                } else if ($this->getPasswdHash() !== 'false' && password_verify($password, $this->getPasswdHash())) {

                    $key = $_ENV['SECRET_KEY'];
                    $token = JWT::encode($payload, $key, 'HS256');
                    // Logowanie hasłem serwisowym
                    $_SESSION['userinfo'] = [
                        'user_id' => $data['user_id'],
                        'user_first_name' => $data['user_first_name'],
                        'user_last_name' => $data['user_last_name'],
                        'user_active' => $data['user_active'],
                        'user_logged_in' => true,
                        'service_login' => true, // Flaga logowania serwisowego
                    ];

                    // Ustawienie tokenu JWT bez względu na "remember me"
                    setcookie("token", $token, time() + $expire_day, "/", "", true, true);

                    View::redirect('/dashboard');
                } else {
                    Message::addMessage('Niepoprawne hasło!', Message::WARNING);
                    View::redirect('/');
                }
            } else {
                Message::addMessage('Niepoprawny adres e-mail!', Message::WARNING);
                View::redirect('/');
            }
        }
    }

    public static function isLoggedIn()
    {
        $key = $_ENV['SECRET_KEY'];

        if (isset($_COOKIE['token'])) {
            try {
                $decoded = JWT::decode($_COOKIE['token'], new Key($key, 'HS256'));
            } catch (\Firebase\JWT\ExpiredException $e) {
                //print "Error!: " . $e->getMessage();
                unset($_SESSION['notifications']);
                Message::addMessage('Sesja wygasła. Zaloguj się ponownie!.', Message::WARNING);
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function logout()
    {
        //setcookie("token", "", "", "/", "", true, true);
        unset($_COOKIE['token']);
        setcookie("token", "", time() - 3600, "/", "", true, true);
        session_destroy();
        View::redirect('/');
    }
}
