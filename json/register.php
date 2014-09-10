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
	
	if (!UserHandler::userExists($username)) {
		$message = 'Brukernavnet du skrev inn er allerede i bruk.';
	} else if (!UserHandler::userExists($email)) {
		$message = 'E-post adressen du skrev inn er allerede i bruk.';
	} else if (empty($firstname) || strlen($firstname) > 32) {
		$message = 'Du har ikke skrevet inn noe fornavn.';
	} else if (empty($lastname) || strlen($lastname) > 32) {
		$message = 'Du har ikke skrevet inn noe etternavn.';	
	} else if (!preg_match('/^[a-zæøåA-ZÆØÅ0-9_-]{2,16}$/', $username)) {
		$message = 'Brukernavnet du skrev inn er ikke gyldig, det må bestå av minst 2 tegn og max 16 tegn.';
	} else if (empty($password)) {
		$message = 'Du har ikke oppgitt noe passord.';
	} else if (strlen($_GET['password']) < 8) {
		$message = 'Passordet du skrev inn er for kort! Det må minst bestå av 8 tegn.';
	} else if (strlen($_GET['password']) > 32) {
		$message = 'Passordet du skrev inn er for langt! Det kan maks bestå av 16 tegn.';
	} else if ($password != $confirmPassword) {
		$message = 'Passordene du skrev inn er ikke like.';
	} else if (empty($email) || !preg_match('/^([a-zæøåA-ZÆØÅ0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$message = 'E-post adressen du skrev inn er ikke gyldig.';
	} else if ($email != $confirmEmail) {
		$message = 'E-post adressene er ikke like.';
	} else if (!is_numeric($gender)) {
		$message = 'Du har oppgitt et ugyldig kjønn.';
	} else if (!is_numeric($phone) || strlen($phone) != 8) {
		$message = 'Du har ikke skrevet inn et gyldig telefonnummer.';
	} else if (empty($address) && strlen($address) > 32) {
		$message = 'Du må skrive inn en adresse.';
	} else if (!is_numeric($postalcode) || strlen($postalcode) != 4 || !CityDictionary::hasPostalCode($postalcode)) {
		$message = 'Du må skrive inn et gyldig postnummer, postnummeret må være 4 tegn langt.';
	} else if (!preg_match('/^[a-zæøåA-ZÆØÅ0-9_-]{2,16}$/', $nickname) && !empty($nickname)) {
		$message = 'Kallenavnet du skrev inn er ikke gyldig, det må bestå av minst 2 tegn og max 16 tegn.';
	} else if (date_diff(date_create($birthdate), date_create('now'))->y < 18 && (!isset($_GET['emergencycontactphone']) || !is_numeric($emergencycontactphone) || strlen($emergencycontactphone) != 8)) {
		if (!is_numeric($emergencycontactphone)) {
			$message = 'Foresattes telefonnummer må være et tall!';
		} else if (strlen($emergencycontactphone) != 8) {
			$message = 'Foresattes telefonnummer er ikke 8 siffer langt!';
		}
		
		$message = 'Du er under 18 år, og må derfor oppgi et telefonnummer til en forelder.';
	} else {
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
			is_numeric($emergencycontactphone)) {
			EmergencyContactHandler::createEmergencyContact($user, $emergencycontactphone);
		}
		
		$user->sendRegistrationMail();
		
		$result = true;
		$message = 'Din bruker har blitt registrert! Du har nå fått en aktiveringslink på e-post. Husk å sjekk søppelpost/spam hvis du ikke finner den.';
	}
} else {
	$message = "Du har ikke fylt inn alle feltene!";
}

echo json_encode(array('result' => $result, 'message' => $message));
?>