<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/permissionhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('admin') ||
		$user->hasPermission('admin-permissions')) {
		if (isset($_GET['userId']) &&
			isset($_GET['value']) &&
			is_numeric($_GET['userId']) &&
			!empty($_GET['value'])) {
			$user = UserHandler::getUser($_GET['userId']);
			$value = $_GET['value'];
			
			PermissionHandler::createPermission($user, $value);
			$result = true;
		} else {
			$message = 'Du har ikke fyllt ut alle feltene!';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>