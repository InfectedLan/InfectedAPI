<?php
require_once 'settings.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/user.php';

session_name('user');
session_start();

class Utils {
	public static function getUser() {
		return self::isAuthenticated() ? $_SESSION['user'] : null;
	}
	
	public static function isAuthenticated() {
		return isset($_SESSION['user']);
	}
	
	public static function getDayFromInt($day) {
		$dayList = array('Mandag', 
					'Tirsdag', 
					'Onsdag', 
					'Torsdag', 
					'Fredag', 
					'Lørdag',
					'Søndag');
		
		return $dayList[$day - 1];
	}
	
	public static function getMonthFromInt($month) {
		$monthList = array('Januar', 
					'Februar', 
					'Mars', 
					'April', 
					'Mai', 
					'Juni', 
					'Juli', 
					'August', 
					'September', 
					'Oktober', 
					'November', 
					'Desember');
		
		return $monthList[$month - 1];
	}
	
	public static function spamCheck($email) {
		
		
		// Validate e-mail address
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	public static function sendEmail($user, $subject, $message) {
		// Sanitize e-mail address
		$to = filter_var($user->getEmail(), FILTER_SANITIZE_EMAIL);
		
		// In case any of our lines are larger than 70 characters, we should use wordwrap().
		$message = wordwrap($message, 70, '\r\n');
		
		// Validate e-mail address.
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			// To send HTML mail, the Content-type header must be set
			$headers = array();
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=UTF-8';
			
			// Additional headers.
			$headers[] = 'To: ' . $user->getFullname() . ' <' . $to . '>';
			$headers[] = 'From: ' . Settings::emailName . ' <' . Settings::email . '>';
			
			// Send the e-mail.
			return mail($to, $subject, $message, implode('\r\n', $headers));
		}
		
		return false;
	}

	public static function hasPermission($permission) {
		return PermissionsHandler::getPermission(self::getUser()->getUsername(), $permission);
	}
}
?>