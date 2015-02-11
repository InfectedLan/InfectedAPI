<?php
require_once 'session.php';

$result = false;
$message = null;
$userData = array();

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') || 
		$user->hasPermission('event.checkin')) {

		if (isset($_GET['id'])
			is_numeric($_GET['id'])) {
			$ticket = TicketHandler::getTicket($_GET['id']);

			if ($ticket != null) {
				if (!$ticket->isCheckedIn()) {
					$ticketUser = $ticket->getUser();

					array_push($userData, array('id' => $ticketUser->getId(),
											    'firstname' => $ticketUser->getFirstname(),
												'lastname' => $ticketUser->getLastname(),
											 	'username' => $ticketUser->getUsername(),
											 	'email' => $ticketUser->getEmail(),
											 	'bithdate' => date('d.m.Y', $ticketUser->getBirthdate()),
											 	'gender' => $ticketUser->getGender(),
											 	'age' => $ticketUser->getAge()));

					$result = true;
				} else {
					$message = 'Denne billetten er allerede sjekket inn!';
				}
			} else {
				$message = 'Denne billetten finnes ikke';
			}
		} else {
			$message = 'Vi mangler felt';
		}
	} else {
		$message = 'Du har ikke tillatelse til dette!';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

if ($result) {
	echo json_encode(array('result' => $result, 'userData' => $userData));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>