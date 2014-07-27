<?php
require_once 'objects/user.php';

session_start();

class Session {
	public static function isAuthenticated() {
		return isset($_SESSION['user']);
	}
	
	public static function getCurrentUser() {
		return self::isAuthenticated() ? $_SESSION['user'] : null;
	}
}
?>