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
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/notehandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('chief.checklist')) {
		if (isset($_GET['title']) &&
			isset($_GET['content']) &&
			isset($_GET['deadlineDate']) &&
			isset($_GET['deadlineTime']) &&
			isset($_GET['notificationTimeBeforeOffset']) &&
			!empty($_GET['title']) &&
			!empty($_GET['content']) &&
			!empty($_GET['deadlineDate']) &&
			!empty($_GET['deadlineTime'])) {
			$private = isset($_GET['private']) ? ($_GET['private'] ? true  : false) : true;
			$group = !$private ? (isset($_GET['groupId']) ? GroupHandler::getGroup($_GET['groupId']) : $user->getGroup()) : null;
			$team = !$private && isset($_GET['teamId']) ? TeamHandler::getTeam($_GET['teamId']) : null;
			$user = !$private ? (isset($_GET['userId']) ? UserHandler::getUser($_GET['userId']) : null) : $user;
			$title = $_GET['title'];
			$content = $_GET['content'];
			$type = true;
			$notificationTimeBeforeOffset = $_GET['notificationTimeBeforeOffset'];
			$deadlineTime = $_GET['deadlineDate'] . ' ' . $_GET['deadlineTime'];
			$done = isset($_GET['done']) ? $_GET['done'] : 0;

			NoteHandler::createNote($group, $team, $user, $title, $content, $type, $notificationTimeBeforeOffset, $deadlineTime, $done);
			$result = true;
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