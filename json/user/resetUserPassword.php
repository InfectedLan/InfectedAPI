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
				$user->sendPasswordResetMail();
				$result = true;
				$message = '<p>En link har blitt sendt til din registrerte e-post adresse, klikk på linken for å endre passordet ditt.</p>';
			}
		} else {
			$message = '<p>Kunne ikke finne brukeren i vår database.</p>';
		}
	} else {
		$message = '<p>Du må skrive inn en e-postadresse eller ett brukernavn!</p>';
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
				UserHandler::updateUserPassword($user->getId(), hash('sha256', $password));
				$result = true;
				$message = '<p>Passordet ditt er nå endret.</p>';
			} else {
				$message = '<p>Passordene du skrev inn var ikke like!</p>';
			}
		} else {
			$message = '<p>Linken du fikk for å resette passwordet ditt er ikke lengre gyldig.</p>';
		}
	} else {
		$message = '<p>Du har ikke fyllt ut alle feltene.</p>';
	}
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>