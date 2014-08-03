<?php
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (isset($_POST['firstname']) && 
	isset($_POST['lastname']) && 
	isset($_POST['username']) &&  
	isset($_POST['password']) && 
	isset($_POST['confirmpassword']) && 
	isset($_POST['email']) && 
	isset($_POST['gender']) && 
	isset($_POST['birthday']) && 
	isset($_POST['birthmonth']) && 
	isset($_POST['birthyear']) && 
	isset($_POST['phone'])) {
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$username = $_POST['username'];
	$password = hash('sha256', $_POST['password']);
	$confirmPassword = hash('sha256', $_POST['confirmpassword']);
	$email = $_POST['email'];
	$gender = $_POST['gender'];
	$birthdate = $_POST['birthyear'] . '-' . $_POST['birthmonth'] . '-' . $_POST['birthday']; 
	$phone = $_POST['phone'];
	$address = $_POST['address'];
	$postalcode = $_POST['postalcode'];
	$nickname = isset($_POST['nickname']) ? $_POST['nickname'] : $username;
	
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
		} else if (strlen($_POST['password']) < 8) {
			$message = 'Passordet er for kort! Det må minst bestå av 8 tegn.';
		} else if (strlen($_POST['password']) > 32) {
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
				UserHandler::createUser($firstname, $lastname, $username, $password, $email, $birthdate, $gender, $phone, $address, $postalcode, $nickname);
				
				// Retrives the user object and sends the activation mail.
				$user = UserHandler::getUserByName($username);
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