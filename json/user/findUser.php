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
$users = [];

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['query']) &&
		strlen($_GET['query']) >= 2) {
		$query = preg_replace('/[^A-Za-z0-9]/', ' ', $_GET['query']);
		$userList = UserHandler::search($query);

		if (!empty($userList)) {
			foreach ($userList as $userValue) {
				if ($userValue->isActivated() ||
					!$userValue->isActivated() && $user->hasPermission('*')) {
					$users[] = ['id' => $userValue->getId(),
											'firstname' => $userValue->getFirstname(),
											'lastname' => $userValue->getLastname(),
											'username' => $userValue->getUsername(),
											'email' => $userValue->getEmail(),
											'nickname' => $userValue->getNickname()];
				}
			}

			$result = true;
		} else {
			$message = Localization::getLocale('no_results_found');
		}
	} else {
		$message = Localization::getLocale('no_keyword_provided');
	}
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message, 'users' => $users), JSON_PRETTY_PRINT);
?>
