<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'libraries/phpmailer/src/PHPMailer.php';
require_once 'libraries/phpmailer/src/Exception.php';

require_once 'settings.php';
require_once 'objects/user.php';

class MailManager {
	/*
	 * Sends an email to the given user.
	 */
	public static function sendEmail(User $user, string $subject, string $message) {
	    // Is email support enabled?
		if (Settings::enableEmails) {
            // Create PHPMailer object.
            $email = new PHPMailer(true);

            try {
                // Set sender and recipient.
                $email->SetFrom(Settings::email, Settings::name);
                $email->addAddress($user->getEmail(), $user->getFullName());

                // Set to use HTML and UTF-8 as charset.
                $email->isHTML(true);  // Set email format to HTML
                $email->CharSet = 'UTF-8'; // Set charset to UTF-8
                $email->WordWrap = 70; // Set word wrap to 70 characters

                // Create subject and body.
                $email->Subject = $subject;
                $email->Body = $message;
                $email->AltBody = 'Denne e-posten krever en e-post klient som støtter visning av HTML innhold.';

                // Sending the e-mail.
                $email->send();
            } catch (Exception $exception) {
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            }
        }
	}

	/*
	 * Sends an email to all given users.
	 */
	public static function sendEmails(array $userList, string $subject, string $message) {
		foreach ($userList as $user) {
			self::sendEmail($user, $subject, $message);
		}
	}
}