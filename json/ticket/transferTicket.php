<?php
require_once 'session.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/tickettransferhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id']) &&
		is_numeric($_GET['id'])) {
		$ticket = TicketHandler::getTicket($_GET['id']);
		
		if ($user->equals($ticket->getUser())) {
			if (isset($_GET['target'])) {
				$targetUser = UserHandler::getUser($_GET['target']);
				
				if ($targetUser != null) {
					TicketTransferHandler::transfer($ticket, $targetUser);
					
					$result = true;
					$message = '<p>Biletten er overført.</p>';
				} else {
					$message = '<p>Målbrukeren eksisterer ikke!</p>';
				}
			} else {
				$message = '<p>Felt mangler! Trenger mål!</p>';
			}
		} else {
			$message = '<p>Du eier ikke billetten!</p>';
		}
	} else {
		$message = '<p>Ugyldig bilett.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>