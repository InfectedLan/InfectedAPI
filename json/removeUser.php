<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$removeUser = UserHandler::getUser($_GET['id']); 
			
			if ($removeUser != null) {
				if (!TicketHandler::hasUserTicket($removeUser)) {
					UserHandler::removeUser($removeUser);
					$result = true;
				} else {
					$message = 'Brukeren har en billett, og kan derfor ikke slettes.';
				}
			} else {
				$message = 'Brukeren finnes ikke.';
			}
		} else {
			$message = 'Det er ikke spessifisert noen bruker.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>