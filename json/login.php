<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (!Session::isAuthenticated()) {
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
				$result = true;
				$message = 'Du er nå logget inn!';
			} else {
				$message = 'Feil brukernavn eller passord.';
			}
		} else {
			$message = 'Feil brukernavn eller passord.';
		}
	} else {
		$message = "Du har ikke skrevet inn et brukernavn og passord.";
	}
} else {
	$message = "Du er allerede logget inn!";
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>