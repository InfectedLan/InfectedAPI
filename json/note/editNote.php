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
require_once 'handlers/notehandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('admin-events')) {
		if (isset($_GET['id']) &&
			isset($_GET['groupId']) &&
			isset($_GET['content']) &&
			isset($_GET['done']) &&
			is_numeric($_GET['id']) &&
			is_numeric($_GET['groupId']) &&
			!empty($_GET['content']) &&
			is_numeric($_GET['done'])) {
			$note = NoteHandler::getNote($_GET['id']);
			$group = GroupHandler::getGroup($_GET['groupId']);
			$team = isset($_GET['teamId']) ? TeamHandler::getTeam($_GET['teamId']) : null;
			$user = isset($_GET['userId']) ? UserHandler::getUser($_GET['userId']) : null;
			$content = $_GET['content'];
			$done = $_GET['done'];

			if ($note != null) {
				NoteHandler::updateEvent($note, $group, $team, $user, $content, $done);
				$result = true;
			} else {
				$message = Localization::getLocale('the_note_does_not_exist');
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
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>
