<?php
require_once 'session.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/tickettransferhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$ticket = TicketHandler::getTicket($_GET['id']);
		
		if ($user->getId() == $ticket->getUser()->getId()) {
			if (isset($_GET['target'])) {
				$target = UserHandler::getUser($_GET['target']);
				
				if ($target != null) {
					TicketTransferHandler::transfer($ticket, $target);
					
					$result = true;
					$message = 'Biletten er overført.';
				} else {
					$message = 'Målbrukeren eksisterer ikke!';
				}
			} else {
				$message = 'Felt mangler! Trenger mål!';
			}
		} else {
			echo '{"result":false, "message":"Du eier ikke biletten!"}';
			return;
		}
	} else {
		$message = 'Ugyldig bilett.';
	}
} else {
	$message = 'Du er ikke logget inn!';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>