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

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/emergencycontacthandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['id']) &&
		is_numeric($_GET['id'])) {
		$editUser = UserHandler::getUser($_GET['id']);
		
		if ($editUser != null) {
			if ($user->hasPermission('*') ||
				$user->equals($editUser)) {
				if (isset($_GET['firstname']) &&
					isset($_GET['lastname']) &&
					isset($_GET['username']) &&
					isset($_GET['email']) &&
					isset($_GET['gender']) &&
					isset($_GET['birthday']) &&
					isset($_GET['birthmonth']) &&
					isset($_GET['birthyear']) &&
					isset($_GET['phone']) &&
					isset($_GET['address']) &&
					isset($_GET['postalcode']) &&
					!empty($_GET['firstname']) &&
					!empty($_GET['lastname']) &&
					!empty($_GET['username']) &&
					!empty($_GET['email']) &&
					is_numeric($_GET['gender']) &&
					is_numeric($_GET['birthday']) &&
					is_numeric($_GET['birthmonth']) &&
					is_numeric($_GET['birthyear']) &&
					is_numeric($_GET['phone']) &&
					!empty($_GET['address']) &&
					is_numeric($_GET['postalcode'])) {
					$editUser = UserHandler::getUser($_GET['id']);
					$firstname = ucfirst($_GET['firstname']);
					$lastname = ucfirst($_GET['lastname']);
					$username = $_GET['username'];
					$email = $_GET['email'];
					$gender = $_GET['gender'];
					$birthdate = $_GET['birthyear'] . '-' . $_GET['birthmonth'] . '-' . $_GET['birthday']; 
					$phone = $_GET['phone'];
					$address = ucfirst($_GET['address']);
					$postalcode = $_GET['postalcode'];
					$nickname = !empty($_GET['nickname']) ? $_GET['nickname'] : $editUser->getUsername();
					$emergencyContactPhone = isset($_GET['emergencycontactphone']) ? $_GET['emergencycontactphone'] : 0;
					
					if ($editUser != null) {
						if ($username != $editUser->getUsername() && UserHandler::userExists($username)) {
							$message = '<p>Brukernavnet du skrev inn er allerede i bruk.</p>';
						} else if ($email != $editUser->getEmail() && UserHandler::userExists($email)) {
							$message = '<p>E-post adressen du skrev inn er allerede i bruk.</p>';
						} else if ($phone != $editUser->getPhone() && UserHandler::userExists($phone)) {
							$message = '<p>Telefon nummeret du skrev inn er allerede i bruk.</p>';
						} else if (empty($firstname) || strlen($firstname) > 32) {
							$message = '<p>Du har ikke skrevet inn noe fornavn.</p>';
						} else if (empty($lastname) || strlen($lastname) > 32) {
							$message = '<p>Du har ikke skrevet inn noe etternavn.</p>';
						} else if (empty($email) || !preg_match('/^([a-zæøåA-ZÆØÅ0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
							$message = '<p>E-post adressen du skrev inn er ikke gyldig.</p>';
						} else if (!is_numeric($gender)) {
							$message = '<p>Du har oppgitt et ugyldig kjønn.</p>';
						} else if (!is_numeric($phone) || $phone <= 0 || strlen($phone) < 8 || strlen($phone) > 8) {
							$message = '<p>Du har ikke skrevet inn et gyldig telefonnummer.</p>';
						} else if (empty($address) && strlen($address) > 32) {
							$message = '<p>Du må skrive inn en adresse.</p>';
						} else if (!is_numeric($postalcode) || strlen($postalcode) > 4 || !CityDictionary::hasPostalCode($postalcode)) {
							$message = '<p>Du må skrive inn et gyldig postnummer.</p>';
						} else if (!preg_match('/^[a-zæøåA-ZÆØÅ0-9_-]{2,16}$/', $nickname)) {
							$message = '<p>Kallenavnet du skrev inn er ikke gyldig, det må bestå av minst 2 tegn og max 16 tegn.</p>';
						} else if (date_diff(date_create($birthdate), date_create('now'))->y < 18 && (!isset($_GET['emergencycontactphone']) || !is_numeric($emergencyContactPhone) || strlen($emergencyContactPhone) != 8)) {
							if (!is_numeric($emergencyContactPhone)) {
								$message = '<p>Foresattes telefonnummer må være et tall!</p>';
							} else if (strlen($emergencyContactPhone) != 8) {
								$message = '<p>Foresattes telefonnummer er ikke 8 siffer langt!</p>';
							}
							
							$message = '<p>Du er under 18 år, og må derfor oppgi et telefonnummer til en forelder.</p>';
						} else {
							UserHandler::updateUser($editUser,
													$firstname, 
													$lastname, 
													$username, 
													$email, 
													$birthdate, 
													$gender, 
													$phone, 
													$address, 
													$postalcode, 
													$nickname);
							
							if (EmergencyContactHandler::hasEmergencyContact($editUser) || 
								isset($_GET['emergencycontactphone']) && is_numeric($emergencyContactPhone)) {
								EmergencyContactHandler::createEmergencyContact($editUser, $emergencyContactPhone);
							}
							
							// Update the user instance form database.
							Session::reload();
							$result = true;
						}
					} else {
						$message = '<p>Brukeren finnes ikke.</p>';
					}
				} else {
					$message = '<p>Du har ikke fyllt ut alle feltene.</p>';
				}
			} else {
				$message = '<p>Du har ikke rettigheter til dette.</p>';
			}
		} else {
			$message = '<p>Brukeren eksisterer ikke.</p>';
		}
	} else {
		$message = '<p>Bruker ikke spessifisert.</p>';
	}
} else {
	$message = '<p>Du er allerede logget inn!</p>';
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>