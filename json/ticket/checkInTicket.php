<?php
require_once 'session.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/checkinstatehandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('event.checkin')) {

		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$ticket = TicketHandler::getTicket($_GET['id']);

			if ($ticket != null) {
				if (!$ticket->isCheckedIn()) {
					$ticket->checkIn();
					
					$result = true;
					$message = '<p>Billetten til "' . $ticket->getUser()->getFullName() . '" er nå sjekket inn.</p>';
				} else {
					$message = '<p>Denne billetten er allerede sjekket inn!</p>';
				}
			} else {
				$message = '<p>Denne billetten finnes ikke.</p>';
			}
		} else {
			$message = '<p>Vi mangler felt.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>