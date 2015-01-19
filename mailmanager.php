<?php
require_once 'settings.php';
require_once 'libraries/phpmailer/PHPMailerAutoload.php';

class MailManager {
	/*
	 * Sends an email to the given user.
	 */
	public static function sendMail($user, $subject, $message) {
		// Create PHPMailer object.
		$mail = new PHPMailer;
		
		// Set sender and recipient.
		$mail->SetFrom(Settings::email, Settings::name);
		$mail->addAddress($user->getEmail(), $user->getFullName());

		// Set to use HTML and UTF-8 as charset.
		$mail->isHTML(true);  // Set email format to HTML
		$mail->CharSet = 'UTF-8'; // Set charset to UTF-8
		$mail->WordWrap = 70; // Set word wrap to 70 characters
		
		// Create subject and body.
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->AltBody = 'Denne e-posten krever en e-post klient som støtter visning av HTML innhold.';
		
		// Sending the e-mail.
		$mail->send();
	}
	
	/*
	 * Sends an email to all given users.
	 */
	public static function sendMailToMany($userList, $subject, $message) {
		foreach ($userList as $user) {
			self::sendMail($user, $subject, $message);
		}
	}
}
?>