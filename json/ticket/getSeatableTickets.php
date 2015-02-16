<?php
require_once 'session.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/tickethandler.php';

$result = false;
$message = null;
$ticketData = array();

if (Session::isAuthenticated()) {
	if (isset($_GET['seatmap'])) {
		$seatmap = SeatmapHandler::getSeatmap($_GET['seatmap']);
		
		if ($seatmap != null) {
			$event = SeatmapHandler::getEvent($seatmap);
			$user = Session::getCurrentUser();
			$tickets = TicketHandler::getTicketsSeatableByUser($user, $event);
			
			foreach($tickets as $ticket) {
				$ticketOwner = $ticket->getUser();
				$data = array();
				$data['id'] = $ticket->getId();
				$data['humanName'] = $ticket->getHumanName();
				$data['owner'] = $ticketOwner->getDisplayName();

				array_push($ticketData, $data);
			}

			$result = true;
		} else {
			$message = 'Arrangementet finnes ikke!';
		}
	} else {
		$message = 'Mangler id.';
	}
} else {
	$message = 'Du er ikke logget inn!';
}

if ($result) {
	echo json_encode(array('result' => $result, 'tickets' => $ticketData));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>