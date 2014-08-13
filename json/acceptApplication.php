<?php
require_once 'session.php';
require_once 'handlers/applicationhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('admin') ||
		$user->isGroupMember() && $user->isGroupLeader()) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$id = $_GET['id'];
			
			ApplicationHandler::acceptApplication($id);
			$result = true;
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