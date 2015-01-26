<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$user = UserHandler::getUser($id);
		
		if (isset($user)) {
			if ($user->isEligibleForCompos()) {
				$result = true;
			} else {
				$message = "Brukeren har ikke lov til å delta i compoer. Har brukeren en gyldig billett?";
			}
		} else {
			$message = "Brukeren eksisterer ikke!";
		}
	} else {
		$message = 'Vi mangler felt';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>