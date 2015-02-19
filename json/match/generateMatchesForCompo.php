<?php
require_once 'session.php';
require_once 'handlers/compohandler.php';

$message = null;
$result = false;

// TODO: This should not be stored in the database as this is constant information, we'll have to move this to some static variable.
if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('event.compo')) {

		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$compo = CompoHandler::getCompo($_GET['id']);

			if ($compo != null) {
				if (isset($_GET['startTime']) && 
					!empty($_GET['startTime']) && 
					isset($_GET['compoSpacing']) && 
					!empty($_GET['compoSpacing'])) {

					if (!CompoHandler::hasGeneratedMatches($compo)) {
						CompoHandler::generateDoubleElimination($compo, $_GET['startTime'], $_GET['compoSpacing']);
						$result = true;
					} else {
						$message = '<p>Compoen har allerede genererte matcher.</p>';
					}
				} else {
					$message = '<p>Felt mangler.</p>';
				}
			} else {
				$message = '<p>Compoen finnes ikke!</p>';
			}
		} else {
			$message = '<p>Felt mangler!</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til å gjøre dette!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>