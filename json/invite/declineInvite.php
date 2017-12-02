<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'localization.php';
require_once 'handlers/invitehandler.php';

$result = false;
$message = null;
$clanId = 0;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$invite = InviteHandler::getInvite($_GET['id']);

		if ($invite != null) {
			if ($user->equals($invite->getUser()) ||
				$user->equals($invite->getClan()->getChief())) {
				$invite->decline();
				$result = true;
			} else {
				$message = Localization::getLocale('you_are_not_the_owner_of_this_invite');
			}
		} else {
			$message = Localization::getLocale('this_invite_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');

if ($result) {
	echo json_encode(array('result' => $result, 'clanId' => $clanId), JSON_PRETTY_PRINT);
} else {
	echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}
Database::cleanup();
?>
