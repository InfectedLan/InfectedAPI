<?php
require_once 'session.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/tickettransferhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id']) &&
		is_numeric($_GET['id'])) {
		$ticket = TicketHandler::getTicket($_GET['id']);
		
		if ($ticket != null) {
			$ticket->revertTransfer($user);
			$result = true;
		} else {
			$message = '<p>Billetten finnes ikke.</p>';
		}
	} else {
		$message = '<p>Ugyldig bilett.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>