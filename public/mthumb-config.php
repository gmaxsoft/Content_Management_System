<?php
/**
 * mthumb-config.php
 *
 * Example mThumb configuration file.
 *
 * @created   4/2/14 11:52 AM
 * @author    Mindshare Studios, Inc.
 * @copyright Copyright (c) 2006-2015
 * @link      http://www.mindsharelabs.com/
 *
 */

// Max sizes
if(!defined('MAX_WIDTH')) {
	define('MAX_WIDTH', 3600);
}
if(!defined('MAX_HEIGHT')) {
	define('MAX_HEIGHT', 3600);
}
if(!defined('MAX_FILE_SIZE')) {
	define ('MAX_FILE_SIZE', 20971520); // 20MB
}

/*
 *  External Sites
 */
global $ALLOWED_SITES;
$ALLOWED_SITES = array(
	'flickr.com'
);

// The rest of the code in this config only applies to Apache mod_userdir  (URIs like /~username)

if(mthumb_in_url('~')) {
	$_SERVER['DOCUMENT_ROOT'] = mthumb_find_wp_root();
}

/**
 *  We need to set DOCUMENT_ROOT in cases where /~username URLs are being used.
 *  In a default WordPress install this should result in the same value as ABSPATH
 *  but ABSPATH and all WP functions are not accessible in the current scope.
 *
 *  This code should work in 99% of cases.
 *
 * @param int $levels
 *
 * @return bool|string
 */
function mthumb_find_wp_root($levels = 9) {

	$dir_name = dirname(__FILE__).'/';

	for($i = 0; $i <= $levels; $i++) {
		$path = realpath($dir_name.str_repeat('../', $i));
		if(file_exists($path.'/wp-config.php')) {
			return $path;
		}
	}

	return FALSE;
}

/**
 *
 * Gets the current URL.
 *
 * @return string
 */
function mthumb_get_url() {
    // Użycie operatora koalescencji null (??) do bezpiecznego dostępu
    // Sprawdza, czy 'HTTPS' istnieje i ma wartość 'on'
    $is_https = ($_SERVER["HTTPS"] ?? 'off') === 'on';
    $s = $is_https ? "s" : "";

    // Pobierz protokół z SERVER_PROTOCOL
    $protocol_part = strtolower($_SERVER["SERVER_PROTOCOL"] ?? 'http/1.1');
    $protocol = substr($protocol_part, 0, strpos($protocol_part, "/")) . $s;

    // Bezpieczne pobranie portu
    $port_num = $_SERVER["SERVER_PORT"] ?? '80'; // Domyślnie 80
    $port = ($port_num == "80" || $port_num == "443") ? "" : (":" . $port_num); // Jeśli 80 lub 443, pomiń port

    // Zabezpieczenie przed brakiem REQUEST_URI i SERVER_NAME (choć te są rzadziej puste)
    $server_name = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $request_uri = $_SERVER['REQUEST_URI'] ?? '/';


    return $protocol . "://" . $server_name . $port . $request_uri;
}

/**
 *
 * Checks to see if $text is in the current URL.
 *
 * @param $text
 *
 * @return bool
 */
function mthumb_in_url($text) {
	if(stristr(mthumb_get_url(), $text)) {
		return TRUE;
	} else {
		return FALSE;
	}
}
