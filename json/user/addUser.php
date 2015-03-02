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
require_once 'handlers/userhandler.php';
require_once 'handlers/emergencycontacthandler.php';
require_once 'handlers/citydictionary.php';

$result = false;
$message = null;

if (isset($_GET['firstname']) && 
	isset($_GET['lastname']) && 
	isset($_GET['username']) &&  
	isset($_GET['password']) && 
	isset($_GET['confirmpassword']) && 
	isset($_GET['email']) && 
	isset($_GET['confirmemail']) && 
	isset($_GET['gender']) && 
	isset($_GET['birthday']) && 
	isset($_GET['birthmonth']) && 
	isset($_GET['birthyear']) && 
	isset($_GET['phone'])) {
	$firstname = ucfirst($_GET['firstname']);
	$lastname = ucfirst($_GET['lastname']);
	$username = $_GET['username'];
	$password = hash('sha256', $_GET['password']);
	$confirmPassword = hash('sha256', $_GET['confirmpassword']);
	$email = $_GET['email'];
	$confirmEmail = $_GET['confirmemail'];
	$gender = $_GET['gender'];
	$birthdate = $_GET['birthyear'] . '-' . $_GET['birthmonth'] . '-' . $_GET['birthday']; 
	$phone = $_GET['phone'];
	$address = ucfirst($_GET['address']);
	$postalcode = $_GET['postalcode'];
	$nickname = !empty($_GET['nickname']) ? $_GET['nickname'] : $username;
	$emergencycontactphone = isset($_GET['emergencycontactphone']) ? $_GET['emergencycontactphone'] : null;
	
	if (UserHandler::userExists($username)) {
		$message = '<p>Brukernavnet du skrev inn er allerede i bruk.</p>';
	} else if (UserHandler::userExists($email)) {
		$message = '<p>E-post adressen du skrev inn er allerede i bruk.</p>';
	} else if (UserHandler::userExists($phone)) {
		$message = '<p>Telefon nummeret du skrev inn er allerede i bruk.</p>';
	} else if (empty($firstname) || strlen($firstname) > 32) {
		$message = '<p>Du har ikke skrevet inn noe fornavn.</p>';
	} else if (empty($lastname) || strlen($lastname) > 32) {
		$message = '<p>Du har ikke skrevet inn noe etternavn.</p>';	
	} else if (!preg_match('/^[a-zæøåA-ZÆØÅ0-9_-]{2,16}$/', $username)) {
		$message = '<p>Brukernavnet du skrev inn er ikke gyldig, det må bestå av minst 2 tegn og max 16 tegn.</p>';
	} else if (empty($password)) {
		$message = '<p>Du har ikke oppgitt noe passord.</p>';
	} else if (strlen($_GET['password']) < 8) {
		$message = '<p>Passordet du skrev inn er for kort! Det må minst bestå av 8 tegn.</p>';
	} else if (strlen($_GET['password']) > 32) {
		$message = '<p>Passordet du skrev inn er for langt! Det kan maks bestå av 16 tegn.</p>';
	} else if ($password != $confirmPassword) {
		$message = '<p>Passordene du skrev inn er ikke like.</p>';
	} else if (empty($email) || !preg_match('/^([a-zæøåA-ZÆØÅ0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$message = '<p>E-post adressen du skrev inn er ikke gyldig.</p>';
	} else if ($email != $confirmEmail) {
		$message = '<p>E-post adressene er ikke like.</p>';
	} else if (!is_numeric($gender)) {
		$message = '<p>Du har oppgitt et ugyldig kjønn.</p>';
	} else if (!is_numeric($phone) || $phone <= 0 || strlen($phone) < 8 || strlen($phone) > 8) {
		$message = '<p>Du har ikke skrevet inn et gyldig telefonnummer.</p>';
	} else if (empty($address) && strlen($address) > 32) {
		$message = '<p>Du må skrive inn en adresse.</p>';
	} else if (!is_numeric($postalcode) || strlen($postalcode) != 4 || !CityDictionary::hasPostalCode($postalcode)) {
		$message = '<p>Du må skrive inn et gyldig postnummer, postnummeret må være 4 tegn langt.</p>';
	} else if (!preg_match('/^[a-zæøåA-ZÆØÅ0-9_-]{2,16}$/', $nickname) && !empty($nickname)) {
		$message = '<p>Kallenavnet du skrev inn er ikke gyldig, det må bestå av minst 2 tegn og max 16 tegn.</p>';
	} else if (date_diff(date_create($birthdate), date_create('now'))->y < 18 && (!isset($_GET['emergencycontactphone']) || !is_numeric($emergencycontactphone) || strlen($emergencycontactphone) != 8)) {
		if (!is_numeric($emergencycontactphone)) {
			$message = '<p>Foresattes telefonnummer må være et tall!</p>';
		} else if (strlen($emergencycontactphone) != 8) {
			$message = '<p>Foresattes telefonnummer er ikke 8 siffer langt!</p>';
		}
		
		$message = '<p>Du er under 18 år, og må derfor oppgi et telefonnummer til en forelder.</p>';
	} else {
		// Creates the user in database.
		$user = UserHandler::createUser($firstname, 
										$lastname, 
										$username, 
										$password, 
										$email, 
										$birthdate, 
										$gender, 
										$phone, 
										$address, 
										$postalcode, 
										$nickname);
		
		if (isset($_GET['emergencycontactphone']) &&
			is_numeric($emergencycontactphone) && 
			$emergencycontactphone > 0 &&
			strlen($emergencycontactphone) < 8 &&
			strlen($emergencycontactphone) > 8) {
			EmergencyContactHandler::createEmergencyContact($user, $emergencycontactphone);
		}
		
		$user->sendRegistrationMail();
		
		$result = true;
		$message = '<p>Din bruker har blitt registrert! Du har nå fått en aktiveringslink på e-post. Husk å sjekk søppelpost/spam hvis du ikke finner den.</p>';
	}
} else {
	$message = '<p>Du har ikke fylt inn alle feltene!</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>