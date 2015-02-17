<?php
require_once 'settings.php';
require_once 'secret.php';

class MySQL {
	/* Opens a connection, to given database if specified */
	public static function open($databaseName) {
		// Create connection
		$database = new mysqli(Settings::db_host, 
							   Secret::db_username, 
							   Secret::db_password,
							   $databaseName);

		/* check connection */
		if ($database->connect_errno()) {
			printf("Connect failed: %s\n", $database->connect_error());
			exit();
		}

		/* Change character set to utf8 */
		if (!$database->set_charset('utf8')) {
			printf('Error loading character set utf8: %s\n', $database->error);
		}
		
		return $database;
	}
}
?>