<?php
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
		
		if (UserHandler::userExists($identifier)) {
			$user = UserHandler::getUserByIdentifier($identifier);
			$storedPassword = $user->getPassword();
			
			if ($user->isActivated()) {
				if ($password == $storedPassword) {
					$_SESSION['user'] = $user;
					$result = true;
				} else {
					$message = 'Feil brukernavn eller passord.';
				}
			} else {
				$message = 'Du må aktivere brukeren din før du kan logge inn.';
			}
		} else {
			$message = 'Feil brukernavn eller passord.';
		}
	} else {
		$message = 'Du har ikke skrevet inn et brukernavn og passord.';
	}
} else {
	$message = 'Du er allerede logget inn!';
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>