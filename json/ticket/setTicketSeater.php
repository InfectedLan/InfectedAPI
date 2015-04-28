<?php
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
require_once 'handlers/tickethandler.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$ticket = TicketHandler::getTicket($_GET['id']);
		
		if ($ticket != null) {
			if ($user->equals($ticket->getUser())) {
				if (isset($_GET['target']) &&
					is_numeric($_GET['target'])) {
					$seaterUser = UserHandler::getUser($_GET['target']);
					
					if ($seaterUser != null) {
						TicketHandler::updateTicketSeater($ticket, $seaterUser);
						
						$result = true;
						$message = Localization::getLocale('the_ticket_has_a_new_seater');
					} else {
						$message = Localization::getLocale('the_specified_seater_does_not_exist');
					}
				} else {
					$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
				}
			} else {
				$message = Localization::getLocale('you_do_not_own_this_ticket');
			}
		} else {
			$message = Localization::getLocale('this_ticket_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('no_ticket_specified');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>