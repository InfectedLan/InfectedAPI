<?php
require_once 'session.php';
require_once 'handlers/storesessionhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'paypal/paypal.php';

$result = false;
$message = null;
$url = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['ticketType']) &&
		isset($_GET['amount']) &&
		is_numeric($_GET['ticketType']) &&
		is_numeric($_GET['amount'])) {
		$ticketType = TicketTypeHandler::getTicketType($_GET['ticketType']);
		$amount = $_GET['amount'];
		$event = EventHandler::getCurrentEvent();
		
		// Only allow users to buy tickets within the booking time period.
		if ($event->isBookingTime()) {
			
			// Verify that this type of ticket actually exists.
			if ($ticketType != null) {
				$availableCount = 0;
				$amountLimit = 10;
				
				// Check that ticket count isn't higher than the limit, and that available ticket count afterwards is still greater than zero.
				if ($amount <= $amountLimit &&
					($availableCount - $amount) >= 0) {
					
					// Check that the user don't already has a reserved set of tickets.
					if (!StoreSessionHandler::hasStoreSession($user)) {
						$price = $ticketType->getPriceForUser($user) * $amount;
						$code = StoreSessionHandler::registerStoreSession($user, $ticketType, $amount, $price);
						$url = PayPal::getPaymentUrl($ticketType, $amount, $code, $user);

						if (isset($url)) {
							$result = true;
						} else {
							$message = 'Noe gikk galt da vi snakket med paypal';
						}
					} else {
						$message = 'Du har allerede en session!';
					}
				} else {
					$message = 'Du har enten valgt for mange billetter, eller så er det utsolgt.';
				}
			} else {
				$message = 'Du har valgt en ugyldig billett type.';
			}
		} else {
			$message = 'Billettsalget har ikke åpnet!';
		}
	} else {
		$message = 'Du har ikke fyllt ut alle feltene.';
	}
} else {
	$message = 'Du er ikke logget inn!';
} 

if ($result) {
	echo json_encode(array('result' => $result, 'url' => $url));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>