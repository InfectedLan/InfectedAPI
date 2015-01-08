<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/checkinstatehandler.php';

$result = false;
$message = null;
$userData = array();

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	if($user->hasPermission("*") || $user->hasPermission("functions.checkin")) {
		if(isset($_GET['id'])) {
			$ticket = TicketHandler::getTicket($_GET['id']);

			if(!CheckinStateHandler::isCheckedIn($ticket)) {
				$ticketOwner = $ticket->getUser();

				$userData['fullName'] = $ticketOwner->getFullName();
				$userData['gender'] = $ticketOwner->getGenderName();
				$userData['birthDate'] = date("j. F Y", $ticketOwner->getBirthdate() );
				$userData['age'] = $ticketOwner->getAge();

				$result = true;
			} else {
				$message = "Denne billetten er allerede sjekket inn!";
			}
		} else {
			$message = 'Vi mangler felt';
		}
	} else {
		$message = "Du har ikke tillatelse til dette!";
	}
} else {
	$message = 'Du er ikke logget inn.';
}

if($result) {
	echo json_encode(array('result' => $result, 'userData' => $userData));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>