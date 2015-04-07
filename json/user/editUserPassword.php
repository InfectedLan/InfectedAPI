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
		
		if ($oldPassword == $user->getPassword()) {
			if ($newPassword == $confirmNewPassword) {
				UserHandler::updateUserPassword($user, hash('sha256', $newPassword));
				
				// Update the user instance form database.
				Session::reload();
				$result = true;
			} else {
				$message = '<p>Passordene du skrev inn var ikke like!</p>';
			}
		} else {
			$message = '<p>Det gamle passordet du skrev inn var ikke riktig.</p>';
		}
	} else {
		$message = '<p>Du har ikke fyllt ut alle feltene.</p>';
	}
} else {
	$message = '<p>Du er allerede logget inn!</p>';
} 

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>