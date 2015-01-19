<?php
require_once 'session.php';

$message = null;

// TODO: This should not be stored in the database as this is constant information, we'll have to move this to some static variable.
if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('functions.compoadmin')) {
		if (isset($_GET['id']) &&
			!empty($_GET['id'])) {
			$message = "todo";
		}
	}
}

echo json_encode(array('message' => $message));
?>