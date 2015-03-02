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
require_once 'handlers/tickethandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/tickettransferhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id']) &&
		is_numeric($_GET['id'])) {
		$ticket = TicketHandler::getTicket($_GET['id']);
		
		if ($user->equals($ticket->getUser())) {
			if (isset($_GET['target']) &&
				is_numeric($_GET['target'])) {
				$targetUser = UserHandler::getUser($_GET['target']);
				
				if ($targetUser != null) {
					$ticket->transfer($targetUser);
					$result = true;
					$message = '<p>Billetten er overført.</p>';
				} else {
					$message = '<p>Målbrukeren eksisterer ikke!</p>';
				}
			} else {
				$message = '<p>Felt mangler! Trenger mål!</p>';
			}
		} else {
			$message = '<p>Du eier ikke billetten!</p>';
		}
	} else {
		$message = '<p>Ugyldig bilett.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn!</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>