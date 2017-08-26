<?php
include 'database.php';
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'localization.php';
require_once 'handlers/tickettypehandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/storesessionhandler.php';
require_once 'paypal/paypal.php';
require_once 'handlers/sysloghandler.php';

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
		if ($user->hasPermission('tickets.byPassBookingTime') ||
			$event->isBookingTime()) {

			// Verify that this type of ticket actually exists.
			if ($ticketType != null) {
				$availableCount = $event->getAvailableTickets();
				$amountLimit = 10;

				// Check that ticket count isn't higher than the limit, and that available ticket count afterwards is still greater than zero.
				if ($amount <= $amountLimit) {
					if (($availableCount - $amount) >= 0) {
						// Check that the user don't already has a reserved set of tickets.
						if (!StoreSessionHandler::hasStoreSession($user)) {
							$price = $ticketType->getPriceByUser($user, $amount);
							$code = StoreSessionHandler::createStoreSession($user, $ticketType, $amount, $price);
							$url = PayPal::getPaymentUrl($ticketType, $amount, $code, $user);

							if (isset($url)) {
								$result = true;
							} else {
								$message = Localization::getLocale('someting_went_wrong_while_talking_to_the_payment_service');
							}
						} else {
							$message = Localization::getLocale('you_already_got_a_session');
							SyslogHandler::log("User tried to get a new a new session", "getPaypalUrl", $user, SyslogHandler::SEVERITY_INFO);
						}
					} else {
						$message = Localization::getLocale('there_are_no_more_tickets_left');
					}
				} else {
					$message = Localization::getLocale('you_have_selected_too_many_tickets_the_limit_is_10');
				}
			} else {
				$message = Localization::getLocale('you_have_selected_an_invalid_ticket_type');
				SyslogHandler::log("User tried to buy an invalid ticket type", "getPaypalUrl", $user, SyslogHandler::SEVERITY_ISSUE, array("ticketType" => ($ticketType != null ? $ticketType->getId() : "null")));
			}
		} else {
			$message = Localization::getLocale('the_ticket_sale_is_not_open_yet');
			
		}
	} else {
		$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'url' => $url), JSON_PRETTY_PRINT);
} else {
	echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}
?>
