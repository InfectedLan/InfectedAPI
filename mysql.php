<?php
require_once 'settings.php';
require_once 'secret.php';

class MySQL {
	/* Opens a connection, to given database if specified */
	public static function open($database) {
		// Create connection
		$mysqli = new mysqli(Settings::db_host, Secret::db_username, Secret::db_password, $database);

		/* check connection */
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}

		/* Change character set to utf8 */
		if (!$mysqli->set_charset('utf8')) {
			printf('Error loading character set utf8: %s\n', $mysqli->error);
		}
		
		return $mysqli;
	}
	
	/* Closes connection */
	public static function close($mysqli) {
		$mysqli->close();
	}
}
?>