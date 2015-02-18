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
			$user = Session::getCurrentUser();
			$ticketList = TicketHandler::getTicketsSeatableByUser($user);
			
			foreach ($ticketList as $ticket) {
				$ticketUser = $ticket->getUser();

				array_push($ticketData, array('id' => $ticket->getId(),
											  'humanName' => $ticket->getHumanName(),
											  'owner' => $ticketUser->getDisplayName()));
			}

			$result = true;
		} else {
			$message = '<p>Arrangementet finnes ikke!</p>';
		}
	} else {
		$message = '<p>Ikke noe seatmap spesifisert.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'tickets' => $ticketData));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>