<?php

class db_conn
{
	private static $connection;

	public static function get()
	{
		if (!isset(self::$connection)) {
			try {
				$options = array(
					PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
				);
				$dsn = constant('DB_DSN');
				$username = constant('DB_USERNAME');
				$password = constant('DB_PASSWORD');
				self::$connection= new PDO($dsn, $username, $password, $options);
			} catch (PDOException $e) {
				print "Error: " . $e->getMessage();
				die();
			}
		}

		return self::$connection;
	}

	public static function close()
	{
		self::$connection = null;
	}
}