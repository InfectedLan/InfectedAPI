<?php
require_once 'session.php';
require_once 'handlers/compohandler.php';

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

				if(isset($_GET['startTime']) && 
					!empty($_GET['startTime']) && 
					isset($_GET['compoSpacing']) && 
					!empty($_GET['compoSpacing'])) {

					if(!CompoHandler::hasGeneratedMatches($compo)) {
						
						CompoHandler::generateDoubleElimination($compo, $_GET['startTime'], $_GET['compoSpacing']);
						$result = true;
					} else {
						$message = "Compoen har allerede genererte matcher";
					}
				} else {
					$message = "Felt mangler";
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