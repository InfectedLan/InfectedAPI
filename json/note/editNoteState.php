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

$result = false;
$message = null;
$data = [];

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('event.checklist')) {
		if (isset($_GET['id']) &&
			is_numeric($_GET['id'])) {
			$note = NoteHandler::getNote($_GET['id']);
			$done = isset($_GET['done']) && $_GET['done'] > 0 ? true : false;
			$inProgress = isset($_GET['inProgress']) && $_GET['inProgress'] > 0 ? true : false;

			if ($note != null) {
				if ($done > 0) {
					$note->setDone($done);
					$inProgress = false;
				} else {
					$note->setInProgress($inProgress);
				}

				$result = true;
				$data[] = ['done' => $done,
								   'inProgress' => $inProgress];
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
echo json_encode(['result' => $result, 'message' => $message, 'data' => $data], JSON_PRETTY_PRINT);
?>
