<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
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
				UserHandler::updateUserPassword($user, hash('sha256', $newPassword));
				
				// Update the user instance form database.
				Session::reload();
				$result = true;
			} else {
				$message = '<p>Passordene du skrev inn var ikke like!</p>';
			}
		} else {
			$message = '<p>Det gamle passordet du skrev inn var ikke riktig.</p>';
		}
	} else {
		$message = '<p>Du har ikke fyllt ut alle feltene.</p>';
	}
} else {
	$message = '<p>Du er allerede logget inn!</p>';
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>