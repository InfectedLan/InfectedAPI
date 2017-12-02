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

require_once 'session.php';
require_once 'database.php';
require_once 'localization.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/sysloghandler.php';

$result = false;
$message = null;

if (!Session::isAuthenticated()) {
	if (isset($_POST['identifier']) &&
		isset($_POST['password']) &&
		!empty($_POST['identifier']) &&
		!empty($_POST['password'])) {
		$identifier = $_POST['identifier'];
		$password = hash('sha256', $_POST['password']);

		if (UserHandler::hasUser($identifier)) {
			$user = UserHandler::getUserByIdentifier($identifier);
			$storedPassword = $user->getPassword();

			if ($user->isActivated()) {
				if (hash_equals($password, $storedPassword)) {
					$_SESSION['userId'] = $user->getId();
					SyslogHandler::log("User logged in", "loginUser", $user);
					$result = true;
				} else {
					$message = Localization::getLocale('wrong_username_or_password');
				}
			} else {
				$message = Localization::getLocale('you_must_activate_your_user_account_in_order_to_logg_in');
			}
		} else {
			$message = Localization::getLocale('this_user_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('you_must_enter_a_username_and_password');
	}
} else {
	$message = Localization::getLocale('you_are_already_logged_in');
}

header('Content-Type: text/plain');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();
?>
