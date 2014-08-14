<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = true;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['oldPassword']) &&
		isset($_GET['newPassword']) &&
		isset($_GET['confirmNewPassword']) &&
		!empty($_GET['oldPassword']) &&
		!empty($_GET['newPassword']) &&
		!empty($_GET['confirmNewPassword'])) {
		$oldPassword = hash('sha256', $_GET['oldPassword']);
		$newPassword = $_GET['newPassword'];
		$confirmNewPassword = $_GET['confirmNewPassword'];
		
		if ($oldPassword == $user->getPassword()) {
			if ($newPassword == $confirmNewPassword) {
				UserHandler::updateUserPassword($user->getId(), hash('sha256', $newPassword));
				
				// Update the user instance form database.
				Session::reload();
				$result = true;
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