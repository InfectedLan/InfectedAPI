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

require_once 'session.php';
require_once 'localization.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['oldPassword']) &&
		isset($_GET['newPassword']) &&
		isset($_GET['confirmNewPassword']) &&
		!empty($_GET['oldPassword']) &&
		!empty($_GET['newPassword']) &&
		!empty($_GET['confirmNewPassword'])) {
		$oldPassword = hash('sha256', $_GET['oldPassword']);
		$newPassword = $_GET['newPassword'];
		$confirmNewPassword = $_GET['confirmNewPassword'];

		if (hash_equals($oldPassword, $user->getPassword()) {
			if ($newPassword == $confirmNewPassword) {
				UserHandler::updateUserPassword($user, hash('sha256', $newPassword));

				// Update the user instance form database.
				Session::reloadCurrentUser();
				$result = true;
			} else {
				$message = Localization::getLocale('passwords_does_not_match');
			}
		} else {
			$message = Localization::getLocale('the_old_password_you_entered_was_incorrect');
		}
	} else {
		$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>
