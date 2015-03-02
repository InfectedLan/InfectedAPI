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

$result = false;
$message = null;
$userData = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') || 
		$user->hasPermission('event.checkin')) {

		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$ticket = TicketHandler::getTicket($_GET['id']);

			if ($ticket != null) {
				if (!$ticket->isCheckedIn()) {
					$ticketUser = $ticket->getUser();

					$userData = array('id' => $ticketUser->getId(),
								      'firstname' => $ticketUser->getFirstname(),
									  'lastname' => $ticketUser->getLastname(),
								 	  'username' => $ticketUser->getUsername(),
								 	  'email' => $ticketUser->getEmail(),
								      'birthdate' => date('d.m.Y', $ticketUser->getBirthdate()),
								 	  'gender' => $ticketUser->getGenderAsString(),
								 	  'age' => $ticketUser->getAge(),
								 	  'phone' => $ticketUser->getPhone(),
								 	  'address' => $ticketUser->getAddress(),
									  'city' => $ticketUser->getPostalCode() . ', ' . $ticketUser->getCity());

					$result = true;
				} else {
					$message = '<p>Denne billetten er allerede sjekket inn!</p>';
				}
			} else {
				$message = '<p>Denne billetten finnes ikke.</p>';
			}
		} else {
			$message = '<p>Vi mangler felt.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'userData' => $userData));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>