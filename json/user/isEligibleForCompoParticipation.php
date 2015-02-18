<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	if (isset($_GET['id']) &&
		is_numeric($_GET['id'])) {
		$user = UserHandler::getUser($_GET['id']);
		
		if ($user != null) {
			if ($user->isEligibleForCompos()) {
				$result = true;
			} else {
				$message = '<p>Brukeren har ikke lov til å delta i compoer. Har brukeren en gyldig billett?</p>';
			}
		} else {
			$message = '<p>Brukeren eksisterer ikke!</p>';
		}
	} else {
		$message = '<p>Vi mangler felt.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>