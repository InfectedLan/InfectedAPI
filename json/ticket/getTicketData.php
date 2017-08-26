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

$result = false;
$message = null;
$userData = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('event.checkin')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$ticket = TicketHandler::getTicket($_GET['id']);

			if ($ticket != null) {
				if (!$ticket->isCheckedIn()) {
					$ticketUser = $ticket->getUser();

					$userData = ['id' => $ticketUser->getId(),
										   'firstname' => $ticketUser->getFirstname(),
										   'lastname' => $ticketUser->getLastname(),
									 	   'username' => $ticketUser->getUsername(),
									 	   'email' => $ticketUser->getEmail(),
										   'birthdate' => date('d.m.Y', $ticketUser->getBirthdate()),
									 	   'gender' => $ticketUser->getGenderAsString(),
									 	   'age' => $ticketUser->getAge(),
									 	   'phone' => $ticketUser->getPhone(),
									 	   'address' => $ticketUser->getAddress(),
										   'city' => $ticketUser->getPostalCode() . ', ' . $ticketUser->getCity()];

					$result = true;
				} else {
					$message = Localization::getLocale('this_ticket_is_already_checked_in');
				}
			} else {
				$message = Localization::getLocale('this_ticket_does_not_exist');
			}
		} else {
			$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
		}
	} else {
		$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(['result' => $result, 'userData' => $userData], JSON_PRETTY_PRINT);
} else {
	echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}
?>
