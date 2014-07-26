<?php
class EmailManager {
	public static function sendMail($user, $subject, $message) {
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
}
?>