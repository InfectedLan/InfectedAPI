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
					CheckInStateHandler::checkIn($ticket);
					$result = true;
					$message = 'Billetten til "' . $ticket->getUser()->getFullName() . '"  er nå sjekket inn.';
				} else {
					$message = 'Denne billetten er allerede sjekket inn!';
				}
			} else {
				$message = 'Denne billetten finnes ikke.';
			}
		} else {
			$message = 'Vi mangler felt.';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette!';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>