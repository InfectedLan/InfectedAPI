<?php
require_once 'session.php';

$message = null;
$result = false;

// TODO: This should not be stored in the database as this is constant information, we'll have to move this to some static variable.
if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('functions.compoadmin')) {

		if (isset($_GET['id']) &&
			!empty($_GET['id'])) {
			$compo = CompoHandler::getCompo($_GET['id']);

			if(isset($compo)) {
				if(!CompoHandler::hasGeneratedMatches($compo)) {
					CompoHandler::generateDoubleElimination($compo);
					$result = true;
				} else {
					$message = "Compoen har allerede genererte matcher";
				}
			} else {
				$message = "Compoen finnes ikke!";
			}
		} else {
			$message = "Felt mangler!";
		}
	} else {
		$message = "Du har ikke tillatelse til å gjøre dette!";
	}
} else {
	$message = "Du er ikke logget inn!";
}

echo json_encode(array('result' => $result, 'message' => $message));
?>