<?php
require_once 'settings.php';
require_once 'secret.php';

class Database {
	/* 
	 * Opens a connection to specified database.
	 */
	public static function open($database) {
		// Create connection
		$mysqli = new mysqli(Settings::db_host, 
							   Secret::db_username, 
							   Secret::db_password,
							   $database);

		// Check connection.
		if ($mysqli->connect_errno) {
			printf('Connect failed: %s\n', $mysqli->connect_error());
			exit();
		}

		// Change character set to utf8.
		if (!$mysqli->set_charset('utf8')) {
			printf('Error loading character set utf8: %s\n', $mysqli->error);
		}
		
		return $mysqli;
	}
}
?>