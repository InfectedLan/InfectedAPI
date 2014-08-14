<?php
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (isset($_GET['firstname']) && 
	isset($_GET['lastname']) && 
	isset($_GET['username']) &&  
	isset($_GET['password']) && 
	isset($_GET['confirmpassword']) && 
	isset($_GET['email']) && 
	isset($_GET['gender']) && 
	isset($_GET['birthday']) && 
	isset($_GET['birthmonth']) && 
	isset($_GET['birthyear']) && 
	isset($_GET['phone'])) {
	$firstname = $_GET['firstname'];
	$lastname = $_GET['lastname'];
	$username = $_GET['username'];
	$password = hash('sha256', $_GET['password']);
	$confirmPassword = hash('sha256', $_GET['confirmpassword']);
	$email = $_GET['email'];
	$gender = $_GET['gender'];
	$birthdate = $_GET['birthyear'] . '-' . $_GET['birthmonth'] . '-' . $_GET['birthday']; 
	$phone = $_GET['phone'];
	$address = $_GET['address'];
	$postalcode = $_GET['postalcode'];
	$nickname = isset($_GET['nickname']) ? $_GET['nickname'] : $username;
	
	if (!UserHandler::userExists($username) || 
		!UserHandler::userExists($email)) {
		if (empty($firstname)) {
			$message = 'Du har ikke skrevet inn noe fornavn.';
		} else if (empty($lastname)) {
			$message = 'Du har ikke skrevet inn noe etternavn.';	
		} else if (empty($username) || strlen($username) < 4) {
			$message = 'Du har ikke oppgitt noe brukernavn, eller det er for kort.';
		} else if (strlen($username) > 32) {
			$message = 'Brukernavnet ditt er for langt!';
		} else if (empty($password)) {
			$message = 'Du har ikke oppgitt noe passord!';
		} else if (strlen($_GET['password']) < 8) {
			$message = 'Passordet er for kort! Det må minst bestå av 8 tegn.';
		} else if (strlen($_GET['password']) > 32) {
			$message = 'Passordet ditt er for langt!';
		/* } else if (!preg_match('#[0-9]+#', $password)) {
			$message = 'Passordet må inneholde minst et tall.';
		} else if (!preg_match('#[a-z]+#', $password)) {
			$message = 'Passordet må inneholde minst en bokstav.';
		} else if (!preg_match('#[A-Z]+#', $password)) {
			$message = 'Passordet må inneholde minst en stor bokstav.'; */
		} else if (strlen($email) > 32 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$message = 'E-posten du skrev inn er ikke en gyldig e-post addresse!';
		} else if ($gender < 0 || $gender > 1) {
			$message = 'Du har oppgitt et ugyldig kjønn.';
		} else if (empty($phone) || strlen($phone) != 8) {
			$message = 'Du har ikke skrevet inn et gyldig telefonnummer.';
		} else if (empty($address)) {
			$message = 'Du må skrive inn en adresse.';
		} else if (empty($postalcode) || strlen($postalcode) != 4) {
			$message = 'Du må skrive inn et gyldig postnummer.';
		} else if (strlen($nickname) > 32) {
			$message = 'Kallenavnet ditt er for langt!';
		} else {
			if ($password == $confirmPassword) {
				// Creates the user in database.
				UserHandler::createUser($firstname, 
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
				
				// Retrives the user object and sends the activation mail.
				$user = UserHandler::getUserByName($username);
				
				if (isset($_GET['emergencycontactphone']) &&
					is_numeric($_GET['emergencycontactphone'])) {
					EmergencyContactHandler::createEmergencyContact($user, $_GET['emergencycontactphone']);
				}
				
				$user->sendRegistrationMail();
				
				$result = true;
				$message = 'Din bruker har blitt laget! Sjekk e-posten din for å aktivere, før du logger inn.';
			} else {
				$message = 'Passordene er ikke like!';
			}
		}
	} else {
		$message = 'Brukeren finnes allerede!';
	}
} else {
	$message = "Du har ikke fylt inn alle feltene!";
}

echo json_encode(array('result' => $result, 'message' => $message));
?>