<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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
require_once 'database.php';
require_once 'handlers/nfcgatehandler.php';
require_once 'handlers/tickethandler.php';
require_once 'localization.php';

$result = false;
$status = http_response_code();
$message = null;
$userData = null;
$ticketData = null;
$authenticated = false;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('event.checkin')) {
		$authenticated = true;
	} else {
		$status = 403; // Forbidden
		$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
	}
} elseif(isset($_GET["pcbid"])) {
	$unit = NfcGateHandler::getGateByPcbid($_GET["pcbid"]);
	if($unit != null) {
		$authenticated = true;
	} else {
		$status = 403; // Forbidden
		$message = Localization::getLocale('invalid_pcbid');
	}
} else {
	$status = 401; // Bad Request.
	$message = Localization::getLocale('you_are_not_logged_in');
}

if($authenticated) {
	if (isset($_GET['ticketId']) &&
		is_numeric($_GET['ticketId'])) {
		$ticket = TicketHandler::getTicket($_GET['ticketId']);

		if ($ticket != null) {
            $ticketUser = $ticket->getUser();
			$userData = ['id' => $ticketUser->getId(),
                         'firstname' => $ticketUser->getFirstname(),
                         'lastname' => $ticketUser->getLastname(),
                         'username' => $ticketUser->getUsername(),
                         'email' => $ticketUser->getEmail(),
                         'birthdate' => $ticketUser->getBirthdate(),
                         'gender' => $ticketUser->getGenderAsString(),
                         'phone' => $ticketUser->getPhone(),
                         'address' => $ticketUser->getAddress(),
                         'postalcode' => $ticketUser->getPostalCode(),
                         'registereddate' => $ticketUser->getRegisterDate(),
                         'city' => $ticketUser->getCity(),
                         'age' => $ticketUser->getAge()];
            $ticketData = ['checkedIn' => $ticket->isCheckedIn(), 'ticketType' => $ticket->getType()->getTitle()];
            $result = true;
		} else {
            $status = 404; // Not Found.
			$message = Localization::getLocale('this_ticket_does_not_exist');
		}
	} else {
        $status = 400; // Bad Request.
		$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
	}
}

http_response_code($status);
header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message, 'userData' => $userData, 'ticketData' => $ticketData], JSON_PRETTY_PRINT);
Database::cleanup();