<?php
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
				$user->getId() == $editUser->getId()) {
				if (isset($_GET['firstname']) &&
					isset($_GET['lastname']) &&
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
					$email = $_GET['email'];
					$gender = $_GET['gender'];
					$birthdate = $_GET['birthyear'] . '-' . $_GET['birthmonth'] . '-' . $_GET['birthday']; 
					$phone = $_GET['phone'];
					$address = ucfirst($_GET['address']);
					$postalcode = $_GET['postalcode'];
					$nickname = !empty($_GET['nickname']) ? $_GET['nickname'] : $editUser->getUsername();
					$emergencycontactphone = isset($_GET['emergencycontactphone']) ? $_GET['emergencycontactphone'] : 0;
					
					if (empty($firstname) || strlen($firstname) > 32) {
						$message = 'Du har ikke skrevet inn noe fornavn.';
					} else if (empty($lastname) || strlen($lastname) > 32) {
						$message = 'Du har ikke skrevet inn noe etternavn.';	
					} else if (!preg_match('/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$message = 'E-post adressen du skrev inn er ikke gyldig.';
					} else if (!is_numeric($gender)) {
						$message = 'Du har oppgitt et ugyldig kjønn.';
					} else if (!is_numeric($phone) || strlen($phone) > 8) {
						$message = 'Du har ikke skrevet inn et gyldig telefonnummer.';
					} else if (empty($address) && strlen($address) > 32) {
						$message = 'Du må skrive inn en adresse.';
					} else if (!is_numeric($postalcode) || strlen($postalcode) > 4 || !CityDictionary::hasPostalCode($postalcode)) {
						$message = 'Du må skrive inn et gyldig postnummer.';
					} else if (!preg_match('/^[a-zæøåA-ZÆØÅ0-9_-]{2,16}$/', $nickname)) {
						$message = 'Kallenavnet du skrev inn er ikke gyldig, det må bestå av minst 2 tegn og max 16 tegn.';
					} else if (date_diff(date_create($birthdate), date_create('now'))->y < 18 && (!isset($_GET['emergencycontactphone']) || !is_numeric($emergencycontactphone) || strlen($emergencycontactphone) != 8)) {
						if (!is_numeric($emergencycontactphone)) {
							$message = 'Foresattes telefonnummer må være et tall!';
						} else if (strlen($emergencycontactphone) != 8) {
							$message = 'Foresattes telefonnummer er ikke 8 siffer langt!';
						}
						
						$message = 'Du er under 18 år, og må derfor oppgi et telefonnummer til en forelder.';
					} else {
						UserHandler::updateUser($editUser->getId(),
												$firstname, 
												$lastname, 
												$editUser->getUsername(), 
												$editUser->getPassword(), 
												$email, 
												$birthdate, 
												$gender, 
												$phone, 
												$address, 
												$postalcode, 
												$nickname);
						
						if (EmergencyContactHandler::hasEmergencyContact($editUser) || 
							isset($_GET['emergencycontactphone']) && is_numeric($emergencycontactphone)) {
							EmergencyContactHandler::createEmergencyContact($editUser, $emergencycontactphone);
						}
						
						// Update the user instance form database.
						Session::reload();
						$result = true;
					}
				} else {
					$message = 'Du har ikke fyllt ut alle feltene.';
				}
			} else {
				$message = 'Du har ikke rettigheter til dette.';
			}
		} else {
			$message = 'Brukeren eksisterer ikke.';
		}
	} else {
		$message = 'Bruker ikke spessifisert.';
	}
} else {
	$message = "Du er allerede logget inn!";
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>