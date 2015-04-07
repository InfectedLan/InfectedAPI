<?php
/**
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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/seathandler.php';

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
					$user->hasPermission('chief.tickets') ||
					$ticket->canSeat($user)) {
					
					if (!$seat->hasTicket()) {
						if ($seat->getEvent()->equals($ticket->getEvent())) {
							TicketHandler::updateTicketSeat($ticket, $seat);
							$result = true;
							$message = '<p>Billetten har fått nytt sete.</p>';
						} else {
							$message = '<p>Billetten og setet er ikke fra samme event!</p>';
						}
					} else {
						$message = '<p>Det er noen som sitter i det setet!</p>';
					}
				} else {
					$message = '<p>Du har ikke tillatelse til å seate den billetten!</p>';
				}
			} else {
				$message = '<p>Setet eksisterer ikke!</p>';
			}
		} else {
			$message = '<p>Biletten eksisterer ikke!</p>';
		}
	} else {
		$message = '<p>Du har ikke fylt inn alle feltene!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>