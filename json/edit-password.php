<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = true;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_POST['oldPassword']) &&
		isset($_POST['newPassword']) &&
		isset($_POST['confirmNewPassword']) &&
		!empty($_POST['oldPassword']) &&
		!empty($_POST['newPassword']) &&
		!empty($_POST['confirmNewPassword'])) {
		$oldPassword = hash('sha256', $_POST['oldPassword']);
		$newPassword = $_POST['newPassword'];
		$confirmNewPassword = $_POST['confirmNewPassword'];
		
		if ($oldPassword == $user->getPassword()) {
			if ($newPassword == $confirmNewPassword) {
				UserHandler::updateUserPassword($user->getId(), hash('sha256', $newPassword));
				$result = true;
				$message = 'Passordet ditt er nå endret.';
			} else {
				$message = 'Passordene du skrev inn var ikke like!';
			}
		} else {
			$message = 'Det gamle passordet du skrev innvar ikke riktig.';
		}
	} else {
		$message = 'Du har ikke fyllt ut alle feltene.';
	}
} else {
	$message = "Du er allerede logget inn!";
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>