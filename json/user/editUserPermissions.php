<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/permissionhandler.php';
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
			
			foreach (PermissionHandler::getPermissions() as $permission) {
				// Only allow changes by admin or user with the "admin.permissions" to give permissions that he is assigned to other users.
				if ($user->hasPermission('*') ||
					$user->hasPermission('admin.permissions') && 
					$user->hasPermission($permission->getValue())) {
					if (isset($_GET['checkbox_' . $permission->getId()])) {
						UserPermissionHandler::createUserPermission($permissionUser, $permission);
					} else {
						UserPermissionHandler::removeUserPermission($permissionUser, $permission);
					}
				}
			}
		
			$result = true;
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