<?php
require_once 'session.php';
require_once 'handlers/tickethandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$ticket = TicketHandler::getTicket($_GET['id']);
		
		if ($ticket != null) {
			if ($user->getId() == $ticket->getOwner()->getId()) {
				if (isset($_GET['target'])) {
					$target = UserHandler::getUser($target);
					TicketHandler::setSeater($ticket, $target);

					$result = true;
					$message = 'Biletten har en ny seater.';
				} else {
					$message = 'Felt mangler! Trenger mål!';
				}
			} else {
				$message = 'Du eier ikke denne billetten!';
			}
		} else {
			
		}
	} else {
		$merssage = 'Ugyldig bilett.';
	}
} else {
	$message = 'Du er ikke logget inn!';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>