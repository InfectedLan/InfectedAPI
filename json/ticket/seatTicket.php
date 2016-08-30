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
require_once 'handlers/seathandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/sysloghandler.php';

$result = false;
$message = null;

if(Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['ticket'])&&
		isset($_GET['seat'])) {
		$ticket = TicketHandler::getTicket($_GET['ticket']);
		$seat = SeatHandler::getSeat($_GET['seat']);
		
		if ($ticket != null) {
			if ($seat != null) {
				if ($user->hasPermission('*') || 
					$user->hasPermission('admin.tickets') ||
					$ticket->canSeat($user)) {
					
					if (!$seat->hasTicket()) {
					    $event = EventHandler::getCurrentEvent();

					    if(($event->getPrioritySeatingTime() <= time() && $user->isEligibleForPreSeating()) || $event->getSeatingTime() <= time()) {
						if ($seat->getEvent()->equals($ticket->getEvent())) {
						    TicketHandler::updateTicketSeat($ticket, $seat);

						    $message = Localization::getLocale('the_ticket_has_a_new_seat');
						    $result = true;
						} else {
						    $message = Localization::getLocale('the_ticket_and_the_seat_are_not_from_the_same_event');
						}
					    } else {
						if($event->getPrioritySeatingTime() < time()) {
						    $message = Localization::getLocale('you_are_not_eligible_for_early_seating');
						    SyslogHandler::log("Hack attack! ", "seatTicket", $user, SyslogHandler::SEVERITY_CRITICAL);
						} else {
						    $message = Localization::getLocale('seating_has_not_opened_yet');
						    SyslogHandler::log("Hack attack! ", "seatTicket", $user, SyslogHandler::SEVERITY_CRITICAL);
						}
					    }
						
					} else {
						$message = Localization::getLocale('this_seat_is_occupied');
					}
				} else {
					$message = Localization::getLocale('you_do_not_have_permission_to_do_that');
				}
			} else {
				$message = Localization::getLocale('this_seat_does_not_exist');
			}
		} else {
			$message = Localization::getLocale('this_ticket_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
?>