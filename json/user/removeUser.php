<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/tickethandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$removeUser = UserHandler::getUser($_GET['id']); 
			
			if ($removeUser != null) {
				if (!TicketHandler::hasTicket($removeUser)) {
					UserHandler::removeUser($removeUser);
					$result = true;
				} else {
					$message = '<p>Brukeren har en billett, og kan derfor ikke slettes.</p>';
				}
			} else {
				$message = '<p>Brukeren finnes ikke.</p>';
			}
		} else {
			$message = '<p>Det er ikke spessifisert noen bruker.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>