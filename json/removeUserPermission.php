<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/userpermissionshandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.permissions')) {
		if (isset($_GET['userId']) &&
			isset($_GET['value']) &&
			is_numeric($_GET['userId']) &&
			!empty($_GET['value'])) {
			$user = UserHandler::getUser($_GET['userId']); 
			$value = $_GET['value']; 
			
			UserPermissionsHandler::removeUserPermission($user, $value);
			$result = true;
		} else {
			$message = 'En ukjent feil oppstod.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>