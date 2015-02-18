<?php
require_once 'session.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$ticket = TicketHandler::getTicket($_GET['id']);
		
		if ($ticket != null) {
			if ($user->equals($ticket->getUser())) {
				if (isset($_GET['target'])) {
					$seaterUser = UserHandler::getUser($_GET['target']);
					
					if ($seaterUser != null) {
						TicketHandler::updateTicketSeater($ticket, $seaterUser);

						$result = true;
						$message = '<p>Biletten har en ny seater.</p>';
					} else {
						$message = '<p>Den oppgitte seateren finnes ikke.</p>';
					}
				} else {
					$message = '<p>Felt mangler!</p>';
				}
			} else {
				$message = '<p>Du eier ikke denne billetten!</p>';
			}
		} else {
			$message = '<p>Billetten finnes ikke.</p>';
		}
	} else {
		$merssage = '<p>Ugyldig bilett.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>