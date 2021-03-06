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
require_once 'handlers/eventhandler.php';
require_once 'handlers/notehandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'handlers/userhandler.php';
require_once 'utils/userutils.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('event.checklist')) {
		if (isset($_GET['id']) &&
			isset($_GET['title']) &&
			isset($_GET['content']) &&
			isset($_GET['secondsOffset']) &&
			is_numeric($_GET['id']) &&
			!empty($_GET['title']) &&
			!empty($_GET['content'])) {
			$note = NoteHandler::getNote($_GET['id']);
			$group = isset($_GET['groupId']) ? GroupHandler::getGroup($_GET['groupId']) : ($note->hasGroup() ? $note->getGroup() : null);
			$team = null;

			if (!isset($_GET['groupId']) || ($group != null && $group->equals($note->getGroup()))) {
				$team = isset($_GET['teamId']) ? TeamHandler::getTeam($_GET['teamId']) : ($note->hasTeam() ? $note->getTeam() : null);
			}

			$delegatedUser = isset($_GET['userId']) ? UserHandler::getUser($_GET['userId']) : ($note->hasUser() ? $note->getUser() : null);
			$title = $_GET['title'];
			$content = $_GET['content'];
			$secondsOffset = $_GET['secondsOffset'];

			// This is the period we allow the time variable to be set, 86400 is the number of secounds in a day.
			$eventDateTimestamp = strtotime(date('Y-m-d', EventHandler::getCurrentEvent()->getStartTime()));
			$newTimestamp = $eventDateTimestamp + $secondsOffset;
			$periodBefore = 1 * 86400; // 1 day before.
			$periodAfter = 2 * 86400; // 2 days after.
			$intersectsTimePeriod = $newTimestamp >= ($eventDateTimestamp - $periodBefore) && // Check if time offset is greather than periodBefore.
															$newTimestamp <= ($eventDateTimestamp + $periodAfter); // Check if time offset is less than periodAfter.

			$time = isset($_GET['time']) && $intersectsTimePeriod ? $_GET['time'] : 0;
			$watchingUserIdList = isset($_GET['watchingUserIdList']) ? $_GET['watchingUserIdList'] : [];

			if ($note != null) {
				NoteHandler::updateNote($note, $group, $team, $delegatedUser, $title, $content, $secondsOffset, $time);

				// If the secondsOffset or time was changed, we flag the note as not notified.
				if (($secondsOffset != $note->getSecondsOffset()) || ($time != $note->getTime())) {
					$note->setNotified(false);
				}

				NoteHandler::updateWatchingUsers($note, UserUtils::fromUserIdList($watchingUserIdList));
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

header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();
?>
