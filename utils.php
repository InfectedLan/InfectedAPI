<?php
require_once 'settings.php';
require_once 'user.php';
require_once 'database.php';

class Utils {
	private $settings;
	private $database;
	
	public function Utils() {
		$this->settings = new Settings();
		$this->database = new Database();
	}
	
	public function getCurrentEvent() {
		$event = end($this->database->getEvents());
	
		return $event;
	}

	public function getUser() {
		return $this->isAuthenticated() ? $_SESSION['user'] : null;
	}
	
	public function isAuthenticated() {
		return isset($_SESSION['user']);
	}
	
	public function getDayFromInt($day) {
		$dayList = array('Mandag', 
					'Tirsdag', 
					'Onsdag', 
					'Torsdag', 
					'Fredag', 
					'Lørdag',
					'Søndag');
		
		return $dayList[$day - 1];
	}
	
	public function getMonthFromInt($month) {
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
	
	public function spamCheck($email) {
		
		
		// Validate e-mail address
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	public function sendEmail($user, $subject, $message) {
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
			$headers[] = 'From: ' . $this->settings->emailName . ' <' . $this->settings->email . '>';
			
			// Send the e-mail.
			return mail($to, $subject, $message, implode('\r\n', $headers));
		}
		
		return false;
	}

	public function hasPermission($permission) {
		return $this->database->getPermission($this->getUser()->getUsername(), $permission);
	}
}
?>