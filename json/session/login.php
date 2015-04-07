<?php
/**
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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
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
				if ($password == $storedPassword) {
					$_SESSION['user'] = $user;
					$result = true;
				} else {
					$message = '<p>Feil brukernavn eller passord.</p>';
				}
			} else {
				$message = '<p>Du må aktivere brukeren din før du kan logge inn.</p>';
			}
		} else {
			$message = '<p>Feil brukernavn eller passord.</p>';
		}
	} else {
		$message = '<p>Du har ikke skrevet inn et brukernavn og passord.</p>';
	}
} else {
	$message = '<p>Du er allerede logget inn!</p>';
} 

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>