<?php
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