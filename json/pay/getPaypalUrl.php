/*
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

<?php
require_once 'session.php';
require_once 'handlers/storesessionhandler.php';
require_once 'handlers/tickettypehandler.php';
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
							$message = '<p>Noe gikk galt da vi snakket med paypal.</p>';
						}
					} else {
						$message = '<p>Du har allerede en session!</p>';
					}
				} else {
					$message = '<p>Du har enten valgt for mange billetter, eller så er det utsolgt.</p>';
				}
			} else {
				$message = '<p>Du har valgt en ugyldig billett type.</p>';
			}
		} else {
			$message = '<p>Billettsalget har ikke åpnet!</p>';
		}
	} else {
		$message = '<p>Du har ikke fyllt ut alle feltene.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
} 

if ($result) {
	echo json_encode(array('result' => $result, 'url' => $url));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>