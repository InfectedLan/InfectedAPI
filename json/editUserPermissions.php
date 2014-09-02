<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/permissionshandler.php';
require_once 'handlers/userpermissionshandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.permissions')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$permissionUser = UserHandler::getUser($_GET['id']);
			
			foreach (PermissionsHandler::getPermissions() as $permission) {
				// Only allow changes by admin or user with the "admin.permissions" to give permissions that he is assigned to other users.
				if ($user->hasPermission('*') ||
					$user->hasPermission('admin.permissions') && 
					$user->hasPermission($permission->getValue())) {
					if (isset($_GET['checkbox_' . $permission->getId()])) {
						UserPermissionsHandler::createUserPermission($permissionUser, $permission->getValue());
					} else {
						UserPermissionsHandler::removeUserPermission($permissionUser, $permission->getValue());
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