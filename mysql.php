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
	public function open($database) {
		// Create connection
		$con = mysqli_connect($this->settings->db_host, $this->secret->db_username, $this->secret->db_password, $database);
		$con->set_charset("utf8");
		
		// Check connection
		if (mysqli_connect_errno($con)) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		return $con;
	}
	
	/* Closes connection */
	public function close($con) {
		mysqli_close($con);
	}
}
?>