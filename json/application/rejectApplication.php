<?php
require_once 'session.php';
require_once 'handlers/applicationhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.applications') ||
		$user->isGroupLeader()) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$id = $_GET['id'];
			
			if (isset($_GET['reason']) &&
				!empty($_GET['reason'])) {
				$reason = $_GET['reason'];
			
				ApplicationHandler::rejectApplication($id, $reason);
				$result = true;
			} else {
				$message = 'Du har ikke oppgitt noen grunn på hvorfor søkneden skal bli avvist.';
			}
		} else {
			$message = 'Ingen søknad spesifisert.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>