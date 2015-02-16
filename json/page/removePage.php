<?php
require_once 'session.php';
require_once 'handlers/pagehandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$$user->hasPermission('admin.website')) {
		
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			PageHandler::removePage($_GET['id']);
			$result = true;
		} else {
			$message = 'Ikke noen side spesifisert.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>