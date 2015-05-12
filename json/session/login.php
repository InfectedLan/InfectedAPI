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

if (!Session::isAuthenticated()) {
	if (isset($_GET['identifier']) &&
		isset($_GET['password']) &&
		!empty($_GET['identifier']) &&
		!empty($_GET['password'])) {
		$identifier = $_GET['identifier'];
		$password = hash('sha256', $_GET['password']);

		if (UserHandler::hasUser($identifier)) {
			$user = UserHandler::getUserByIdentifier($identifier);
			$storedPassword = $user->getPassword();
			
			if ($user->isActivated()) {
				if (hash_equals($password, $storedPassword)) {
					// If we should remember the user, we store a cookie for 1 month.
					if (isset($_GET['remember'])) {
						setcookie('rememberUser', 'identifier=' . $username . '&password=' . $password, time() + (3600 * 24 * 30);
					}

					$_SESSION['user'] = $user;
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
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>