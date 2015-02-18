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
		
		if (UserHandler::hasUser($identifier)) {
			$user = UserHandler::getUserByIdentifier($identifier);
			$storedPassword = $user->getPassword();
			
			if ($user->isActivated()) {
				if ($password == $storedPassword) {
					$_SESSION['user'] = $user;
					$result = true;
				} else {
					$message = '<p>Feil brukernavn eller passord.</p>';
				}
			} else {
				$message = '<p>Du må aktivere brukeren din før du kan logge inn.</p>';
			}
		} else {
			$message = '<p>Feil brukernavn eller passord.</p>';
		}
	} else {
		$message = '<p>Du har ikke skrevet inn et brukernavn og passord.</p>';
	}
} else {
	$message = '<p>Du er allerede logget inn!</p>';
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>