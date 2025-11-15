<?php
// install.php - Instalator CMS Goliath

// Funkcja do sprawdzania wymagań systemowych
function checkRequirements() {
    $requirements = [
        'php_version' => [
            'required' => '8.2.0',
            'current' => phpversion(),
            'status' => version_compare(phpversion(), '8.2.0', '>=') ? 'ok' : 'error',
            'message' => 'PHP w wersji minimum 8.2'
        ],
        'gd' => [
            'status' => extension_loaded('gd') ? 'ok' : 'error',
            'message' => 'Rozszerzenie GD'
        ],
        'imagick' => [
            'status' => extension_loaded('imagick') ? 'ok' : 'error',
            'message' => 'Rozszerzenie Imagick'
        ]
    ];
    return $requirements;
}

// Funkcja do generowania pliku .env na podstawie danych z formularza
function generateEnvFile($data) {
    $envContent = "";
    foreach ($data as $key => $value) {
        $envContent .= strtoupper($key) . " = '" . addslashes($value) . "'\n";
    }
    file_put_contents('.env', $envContent);
}

// Funkcja do testowania połączenia z bazą danych
function testDatabaseConnection($host, $user, $pass, $name) {
    $mysqli = new mysqli($host, $user, $pass, $name);
    if ($mysqli->connect_error) {
        return ['status' => 'error', 'message' => $mysqli->connect_error];
    }
    return ['status' => 'ok', 'message' => 'Połączenie udane'];
}

// Funkcja do importowania pliku SQL
function importSqlFile($host, $user, $pass, $name, $sqlFile) {
    $mysqli = new mysqli($host, $user, $pass, $name);
    if ($mysqli->connect_error) {
        return ['status' => 'error', 'message' => $mysqli->connect_error];
    }

    $sql = file_get_contents($sqlFile);
    if ($mysqli->multi_query($sql)) {
        do {
            if ($result = $mysqli->store_result()) {
                $mysqli->free_result($result);
            }
        } while ($mysqli->next_result());
    }

    if ($mysqli->error) {
        return ['status' => 'error', 'message' => $mysqli->error];
    }

    return ['status' => 'ok', 'message' => 'Baza danych zaimportowana pomyślnie'];
}

// Obsługa formularza
$step = isset($_GET['step']) ? $_GET['step'] : 'requirements';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 'config') {
        // Zbieranie danych z formularza
        $data = [
            'debug' => isset($_POST['debug']) ? 'true' : 'false',
            'db_driver' => $_POST['db_driver'] ?? 'mysql',
            'db_host' => $_POST['db_host'] ?? '',
            'db_name' => $_POST['db_name'] ?? '',
            'db_user' => $_POST['db_user'] ?? '',
            'db_pass' => $_POST['db_pass'] ?? '',
            'frontend_url' => $_POST['frontend_url'] ?? '',
            'secret_key' => $_POST['secret_key'] ?? bin2hex(random_bytes(64)), // Generuj losowy jeśli pusty
            'default_block_lang' => $_POST['default_block_lang'] ?? '1',
            'sliderfilespath' => $_POST['sliderfilespath'] ?? 'upload/sliderImg/'
        ];

        // Generuj .env
        generateEnvFile($data);

        // Testuj połączenie
        $connTest = testDatabaseConnection($data['db_host'], $data['db_user'], $data['db_pass'], $data['db_name']);
        if ($connTest['status'] === 'error') {
            $message = '<div style="color: red;">Błąd połączenia: ' . $connTest['message'] . '</div>';
        } else {
            // Importuj SQL
            $importResult = importSqlFile($data['db_host'], $data['db_user'], $data['db_pass'], $data['db_name'], 'goliath.sql');
            if ($importResult['status'] === 'ok') {
                $message = '<div style="color: green;">Instalacja zakończona sukcesem! Usuń plik install.php dla bezpieczeństwa.</div>';
                $step = 'complete';
            } else {
                $message = '<div style="color: red;">Błąd importu SQL: ' . $importResult['message'] . '</div>';
            }
        }
    }
}

// Sprawdź, czy .env już istnieje - jeśli tak, instalacja zakończona
if (file_exists('.env') && $step !== 'complete') {
    $step = 'complete';
    $message = '<div style="color: green;">Instalacja już zakończona. Usuń plik install.php.</div>';
}

$requirements = checkRequirements();
$allRequirementsOk = true;
foreach ($requirements as $req) {
    if ($req['status'] === 'error') {
        $allRequirementsOk = false;
        break;
    }
}

if ($step === 'requirements' && $allRequirementsOk) {
    $step = 'config';
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Instalator CMS Goliath</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 600px; margin: auto; }
        .tick { color: green; font-weight: bold; }
        .cross { color: red; font-weight: bold; }
        form { margin-top: 20px; }
        label { display: block; margin: 10px 0 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Instalator CMS Goliath</h1>
        <?php echo $message; ?>

        <?php if ($step === 'requirements'): ?>
            <h2>Sprawdzanie wymagań systemowych</h2>
            <ul>
                <?php foreach ($requirements as $key => $req): ?>
                    <li>
                        <?php echo $req['message']; ?>: 
                        <?php if ($req['status'] === 'ok'): ?>
                            <span class="tick">&#10004; OK (<?php echo isset($req['current']) ? $req['current'] : ''; ?>)</span>
                        <?php else: ?>
                            <span class="cross">&#10008; Brakuje</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if ($allRequirementsOk): ?>
                <p>Wszystkie wymagania spełnione. Przejdź do konfiguracji.</p>
                <a href="?step=config"><button>Dalej</button></a>
            <?php else: ?>
                <p style="color: red;">Nie wszystkie wymagania są spełnione. Popraw i odśwież stronę.</p>
            <?php endif; ?>

        <?php elseif ($step === 'config'): ?>
            <h2>Konfiguracja</h2>
            <form method="POST">
                <label>DEBUG (true/false):</label>
                <input type="text" name="debug" value="true">

                <label>DB_DRIVER:</label>
                <input type="text" name="db_driver" value="mysql">

                <label>DB_HOST:</label>
                <input type="text" name="db_host" value="localhost" required>

                <label>DB_NAME:</label>
                <input type="text" name="db_name" value="goliath" required>

                <label>DB_USER:</label>
                <input type="text" name="db_user" value="root" required>

                <label>DB_PASS:</label>
                <input type="password" name="db_pass" value="">

                <label>FRONTEND_URL:</label>
                <input type="text" name="frontend_url" value="http://localhost:3000">

                <label>SECRET_KEY (zostaw puste, aby wygenerować losowy):</label>
                <input type="text" name="secret_key" value="">

                <label>DEFAULT_BLOCK_LANG:</label>
                <input type="text" name="default_block_lang" value="1">

                <label>SLIDERFILESPATH:</label>
                <input type="text" name="sliderfilespath" value="upload/sliderImg/">

                <button type="submit">Zapisz i zainstaluj</button>
            </form>

        <?php elseif ($step === 'complete'): ?>
            <h2>Instalacja zakończona</h2>
            <p>CMS Goliath został zainstalowany. Usuń plik install.php oraz goliath.sql dla bezpieczeństwa.</p>
            <p>Przejdź do panelu administracyjnego: <a href="/dashboard/">Dashboard</a></p>
        <?php endif; ?>
    </div>

    <script>
        // Prosty JavaScript do walidacji formularza (opcjonalny)
        document.querySelector('form')?.addEventListener('submit', function(e) {
            let requiredFields = document.querySelectorAll('input[required]');
            for (let field of requiredFields) {
                if (!field.value.trim()) {
                    alert('Wypełnij wszystkie wymagane pola!');
                    e.preventDefault();
                    return;
                }
            }
        });
    </script>
</body>
</html>