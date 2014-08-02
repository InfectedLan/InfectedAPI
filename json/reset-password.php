<?php
require_once 'handlers/userhandler.php';
require_once 'handlers/passwordresetcodehandler.php';

$result = false;
$message = null;

if (!isset($_GET['key'])) {
	if (isset($_POST['username'])) {
		$username = $_POST['username'];
		
		if (UserHandler::userExists($username)) {
			$user = UserHandler::getUserByName($username);
			
			if ($user != null) {
				$user->sendPasswordResetMail();
				$result = true;
				$message = 'En link har blitt sendt til din registrerte e-post adresse, klikk på linken for å endre passordet ditt.';
			}
		} else {
			$message = 'Kunne ikke finne brukeren i vår database.';
		}
	} else {
		$message = 'Du må skrive inn en e-postadresse eller ett brukernavn!';
	}
} else {
	if (isset($_POST['password']) &&
		isset($_POST['confirmpassword']) &&
		!empty($_POST['password']) &&
		!empty($_POST['confirmpassword'])) {
		$code = $_GET['key'];
		$password = $_POST['password'];
		$confirmPassword = $_POST['confirmpassword'];
		
		echo $code;
		
		if (PasswordResetCodeHandler::hasPasswordResetCode($code)) {
			$user = PasswordResetCodeHandler::getUserFromPasswordResetCode($code);
			
			if ($password == $confirmPassword) {
				PasswordResetCodeHandler::removePasswordResetCode($code);
				UserHandler::updateUserPassword($user->getId(), hash('sha256', $password));
				$result = true;
				$message = 'Passordet ditt er nå endret.';
			} else {
				$message = 'Passordene du skrev inn var ikke like!';
			}
		} else {
			$message = 'Linken du fikk for å resette passwordet ditt er ikke lengre gyldig.';
		}
	} else {
		$message = 'Du har ikke fyllt ut alle feltene.';
	}
}

echo json_encode(array('result' => $result, 'message' => $message));
?>