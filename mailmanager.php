<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'libraries/phpmailer/PHPMailerAutoload.php';
require_once 'objects/user.php';

class MailManager {
	/*
	 * Sends an email to the given user.
	 */
	public static function sendEmail(User $user, $subject, $message) {
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
	public static function sendEmails(array $userList, $subject, $message) {
		foreach ($userList as $user) {
			self::sendMail($user, $subject, $message);
		}
	}
}
?>
