<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');

define('DB_DSN', 'mysql:host=127.0.0.1;dbname=sh');
define('DB_USERNAME', 'sh');
define('DB_PASSWORD', '545124315@qq.com');

define('AUTH_Y', 1);
define('AUTH_N', 0);

define('RET_OK', 0);
define('RET_NOT_OK', 1);
define('RET_UNKNOWN', 3);

define('LOGIN', 'signin');

$signins = array(
	array('username'=>'sh', 'password'=>'545124315@qq.com'),
);

session_start();

if (basename($_SERVER['SCRIPT_NAME']) != 'signin.php') {
	session_write_close();
	if (!isset($_SESSION[constant('LOGIN')])) {
		header('Location: signin.php');
	}
}
