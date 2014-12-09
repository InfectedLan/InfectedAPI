<?php
require_once 'session.php';
require_once 'handlers/permissionshandler.php';

$message = null;

// TODO: This should not be stored in the database as this is constant information, we'll have to move this to some static variable.
if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.permissions')) {
		if (isset($_GET['value']) &&
			!empty($_GET['value'])) {
			$permission = PermissionsHandler::getPermissionByValue($_GET['value']); 
			
			if ($permission != null) {				
				$message = $permission->getDescription();
			} else {
				$message = 'Ukjent tilgang.';
			}
		}
	}
}

echo json_encode(array('message' => $message));
?>