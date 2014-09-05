<?php
require_once 'session.php';
require_once 'handlers/gameapplicationhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') || 
		$user->hasPermission('functions.site-list-games')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			GameApplicationHandler::removeGameApplication($_GET['id']);
			$result = true;
		} else {
			$message = 'Ikke noe spill søknad spesifisert.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>