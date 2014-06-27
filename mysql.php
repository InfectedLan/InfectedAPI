<?php
require_once 'settings.php';
require_once 'secret.php';

class MySQL {
	private $settings;
	private $secret;
	
	public function __construct() {
        $this->settings = new Settings();
        $this->secret = new Secret();
    }
	
	/* Opens a connection, to given database if specified */
	public static function open($database) {
		
		// Create connection
		$con = mysqli_connect(Settings::db_host, Secret::db_username, Secret::db_password, $database);
		$con->set_charset("utf8");
		
		// Check connection
		if (mysqli_connect_errno($con)) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		return $con;
	}
	
	/* Closes connection */
	public static function close($con) {
		mysqli_close($con);
	}
}
?>