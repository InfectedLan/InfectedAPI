<?php
require_once 'session.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/seathandler.php';

$result = false;
$message = null;

if(Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['ticket'])&&
		isset($_GET['seat'])) {
		$ticket = TicketHandler::getTicket($_GET['ticket']);
		$seat = SeatHandler::getSeat($_GET['seat']);
		
		if ($ticket != null) {
			if ($seat != null) {
				if ($user->hasPermission('*') || 
					$user->hasPermission('chief.tickets') ||
					$ticket->canSeat($user)) {
					
					if (!$seat->hasTicket()) {
						if ($seat->getEvent()->equals($ticket->getEvent())) {
							TicketHandler::updateTicketSeat($ticket, $seat);
							$result = true;
							$message = '<p>Billetten har fått nytt sete.</p>';
						} else {
							$message = '<p>Billetten og setet er ikke fra samme event!</p>';
						}
					} else {
						$message = '<p>Det er noen som sitter i det setet!</p>';
					}
				} else {
					$message = '<p>Du har ikke tillatelse til å seate den billetten!</p>';
				}
			} else {
				$message = '<p>Setet eksisterer ikke!</p>';
			}
		} else {
			$message = '<p>Biletten eksisterer ikke!</p>';
		}
	} else {
		$message = '<p>Du har ikke fylt inn alle feltene!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>