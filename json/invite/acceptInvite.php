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
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';

$result = false;
$message = null;
$clanId = 0;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$invite = InviteHandler::getInvite($_GET['id']);
		
		if ($invite != null) {
			if ($user->equals($invite->getUser())) {
				$invite->accept();
				$result = true;
			} else {
				$message = '<p>Denne invitasjonen er ikke din!</p>';
			}
		} else {
			$message = '<p>Invitasjonen finnes ikke!</p>';
		}
	} else {
		$message = '<p>Felt mangler!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clanId));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>