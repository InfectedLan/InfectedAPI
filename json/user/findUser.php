/*
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;
$users = array();

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
					array_push($users, array('id' => $userValue->getId(),
											 'firstname' => $userValue->getFirstname(),
											 'lastname' => $userValue->getLastname(),
											 'username' => $userValue->getUsername(),
											 'email' => $userValue->getEmail(),
											 'nickname' => $userValue->getNickname()));
				}
			}
			
			$result = true;
		} else {
			$message = '<p>Fant ingen resultater.</p>';
		}
	} else {
		$message = '<p>Ikke noe søkeord oppgitt.</p>';
	}
}

echo json_encode(array('result' => $result, 'message' => $message, 'users' => $users));
?>