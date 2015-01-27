<?php
require_once 'session.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/tickettransferhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$ticketToRevert = TicketHandler::getTicket($_GET['id']);
		
		if (isset($ticketToRevert)) {
			$transferResult = TicketTransferHandler::revertTransfer($ticketToRevert, $user);
			
			if (!isset($transferResult)) {
				$result = true;
			} else {
				$message = $transferResult;
			}
		} else {
			$message = "Billetten finnes ikke!";
		}
	} else {
		$message = 'Ugyldig bilett.';
	}
} else {
	$message = 'Du er ikke logget inn!';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>