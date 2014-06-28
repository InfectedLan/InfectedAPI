<?php
require_once 'handlers/UserHandler.php';

session_start();

$action = isset($_GET['action']) ? $_GET['action'] : 0;
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$returnPage = isset($_GET['returnPage']) ? '../index.php?page=' . $_GET['returnPage'] : '../';

if (isset($_GET['action'])) {
	/* Login user */
	if ($action == 1) {
		if (isset($_POST['username']) &&
			isset($_POST['password']) &&
			!empty($_POST['username']) &&
			!empty($_POST['password'])) {
			$username = $_POST['username'];
			$password = hash('sha256', $_POST['password']);
			
			if (UserHandler::userExists($username)) {
				$user = UserHandler::getUserByName($username);
				$storedPassword = $user->getPassword();
				
				if ($password == $storedPassword) {
					$_SESSION['user'] = $user;
					$message = 'Du er nå logget inn!';
				}
			} else {
				$message = 'Feil brukernavn eller passord.';
			}
		} else {
			$message = 'Du har ikke skrevet inn et brukernavn og passord.';
		}
		
		return;
	/* Logout user */
	} else if ($action == 2) {
		if (isset($_SESSION['user'])) {
			unset($_SESSION['user']);
			$message = 'Du er nå logget ut!';
		}
		
		return;
	/* Register a new user */
	} else if ($action == 3) {
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
			$birthDate = $_POST['birthyear'] . '-' . $_POST['birthmonth'] . '-' . $_POST['birthday'];
			$phone = $_POST['phone'];
			$address = $_POST['address'];
			$postalCode = $_POST['postalCode'];
			$nickname = isset($_POST['nickname']) ? $_POST['nickname'] : $username;
			
			if (!UserHandler::userExists($username)) {
				if (empty($firstname)) {
					$message = '<p>Du har ikke skrevet inn noe fornavn.</p>';
					
					return;
				}
				
				if (empty($lastname)) {
					$message = '<p>Du har ikke skrevet inn noe etternavn.</p>';
					
					return;
				}
				
				if (empty($username) || strlen($username) < 4) {
					$message = '<p>Du har ikke oppgitt noe brukernavn, eller det er for kort.</p>';
					
					return;
				}
				
				if (strlen($username) > 32) {
					$message = '<p>Brukernavnet ditt er for langt!</p>';
					
					return;
				}
				
				if (empty($password)) {
					$message = '<p>Du har ikke oppgitt noe passord!</p>';
				}
				
				if (strlen($password) < 8) {
					$message = '<p>Passordet er for kort!</p>';
					
					return;
				}
				
				if (strlen($password) > 32) {
					$message = '<p>Passordet ditt er for langt!</p>';
					
					return;
				}

				if (!preg_match('#[0-9]+#', $password)) {
					$message = '<p>Passordet må inneholde et tall.</p>';
					
					return;
				}
				
				if (!preg_match('#[a-z]+#', $password)) {
					$message = '<p>Passordet må inneholde en bokstav.</p>';
					
					return;
				}
				
				if (!preg_match('#[A-Z]+#', $password)) {
					$message = '<p>Passordet må inneholde em stor bokstav.</p>';
					
					return;
				}
				
				if (strlen($email) <= 32 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$message = '<p>E-posten du skrev inn er ikke en gyldig e-post addresse!</p>';
					
					return;
				}
			
				if ($gender < 0 || $gender > 1) {
					$message = '<p>Du har oppgitt et ugyldig kjønn.</p>';
				}
				
				if (empty($phone) || strlen($phone) != 8) {
					$message = '<p>Du har ikke skrevet inn et gyldig telefonnummer.</p>';
				}
			
				if (empty($address)) {
					$message = '<p>Du må skrive inn en adresse.</p>';
				}
			
				if (empty($postalCode) || strlen($postalCode) != 4) {
					$message = '<p>Du må skrive inn et gyldig postnummer.</p>';
				}
				
				if (strlen($nickname) > 32) {
					$message = 'Kallenavnet ditt er for langt!';
				}
				
				// Check if passwords match.
				if ($password == $confirmPassword) {
					UserHandler::createUser($firstname, $lastname, $username, $password, $email, $gender, $birthDate, $phone, $address, $postalCode, $nickname);
				} else {
					$message = '<p>Passordene er ikke like!</p>';
				}
			} else {
				$message = '<p>Brukeren finnes allerede!</p>';
			}
		}
		
		return;
	/* Remove user */
	} else if ($action == 4) {
		if (isset($_GET['id'])) {
			UserHandler::removeUser($id);
			$message = '<p>Brukeren ble fjernet!</p>';
			
			return;
		}
	/* Update user */
	} else if ($action == 5) {
		if (isset($_GET['id'])) {
			return;
		}
	} else if ($action == 6) {
		if (isset($_POST['email'])) {
			$user = UserHandler::getUserByName($_POST['email']);
			
			if ($user->sendForgottenEmail()) {
				$message = '<p>Vi har nå sendt deg en e-post med link for å tilbakestille passordet.</p>';
			}
		}
	}
}

if (!empty($message)) {
	header('Location: ' . $returnPage . urlencode($message));
} else {
	header('Location: ' . $returnPage);
}
?>