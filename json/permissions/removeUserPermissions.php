<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/userpermissionhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.permissions')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$permissionUser = UserHandler::getUser($_GET['id']);
			
			if ($permissionUser != null) {
				UserPermissionHandler::removeUserPermissions($permissionUser);
				$result = true;
			} else {
				$message = '<p>Brukeren finnes ikke.</p>';
			}
		} else {
			$message = '<p>Bruker ikke spesifisert.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>