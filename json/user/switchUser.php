<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('developer.change-user')) {
		if (isset($_GET['userId']) &&
			is_numeric($_GET['userId'])) {
			$changeUser = UserHandler::getUser($_GET['userId']);

			if ($changeUser != null) {
				$_SESSION['user'] = $changeUser;
				$result = true;
			} else {
				$message = '<p>Brukeren du prøvde å bytte til eksisterer ikke.</p>';
			}
		} else {
			$message = 'Du har ikke fyllt ut alle feltene.';
		}
	} else {
		$message = 'Du har ikke rettigheter til dette.';
	}
} else {
	$message = "Du er allerede logget inn!";
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>