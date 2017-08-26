<?php
include 'database.php';
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

require_once 'localization.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/passwordresetcodehandler.php';

$result = false;
$message = null;

if (!isset($_GET['code'])) {
	if (isset($_GET['identifier']) &&
		!empty($_GET['identifier'])) {

		$identifier = $_GET['identifier'];

		if (UserHandler::hasUser($identifier)) {
			$user = UserHandler::getUserByIdentifier($identifier);

			if ($user != null) {
				$user->sendPasswordResetEmail();
				$result = true;
				$message = Localization::getLocale('an_email_has_been_sent_to_your_registered_address_click_the_link_to_change_your_password');
			}
		} else {
			$message = Localization::getLocale('could_not_find_the_user_in_the_database');
		}
	} else {
		$message = Localization::getLocale('you_must_enter_a_username_an_email_address_or_a_phone_number');
	}
} else {
	if (isset($_GET['password']) &&
		isset($_GET['confirmpassword']) &&
		!empty($_GET['password']) &&
		!empty($_GET['confirmpassword'])) {
		$code = $_GET['code'];
		$password = $_GET['password'];
		$confirmPassword = $_GET['confirmpassword'];

		if (PasswordResetCodeHandler::hasPasswordResetCode($code)) {
			$user = PasswordResetCodeHandler::getUserFromPasswordResetCode($code);

			if ($password == $confirmPassword) {
				PasswordResetCodeHandler::removePasswordResetCode($code);
				UserHandler::updateUserPassword($user, hash('sha256', $password));
				$result = true;
				$message = Localization::getLocale('your_password_is_now_changed');
			} else {
				$message = Localization::getLocale('passwords_does_not_match');
			}
		} else {
			$message = Localization::getLocale('the_link_to_reset_your_password_is_no_longer_valid');
		}
	} else {
		$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
	}
}

header('Content-Type: text/plain');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
?>
