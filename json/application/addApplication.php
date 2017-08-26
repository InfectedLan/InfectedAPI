<?php
include 'database.php';
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
require_once 'handlers/eventhandler.php';
require_once 'handlers/applicationhandler.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	// Only allow non-members to apply.
	if (!$user->isGroupMember()) {
		// Check that the user has an cropped avatar.
		if ($user->hasCroppedAvatar()) {
			if (isset($_GET['groupId']) &&
				isset($_GET['content']) &&
				is_numeric($_GET['groupId']) &&
				!empty($_GET['content'])) {
				$group = GroupHandler::getGroup($_GET['groupId']);
				$content = $_GET['content'];

				if (!ApplicationHandler::hasUserApplicationsByGroup($user, $group)) {
					ApplicationHandler::createApplication($group, $user, $content);
					$result = true;

					$message = Localization::getLocale('your_appliction_to_value_is_now_submitted', $group->getTitle());
				} else {
					$message = Localization::getLocale('you_have_already_submitted_an_application_to_value_you_can_apply_again_if_your_application_should_be_denied', $group->getTitle());
				}
			} else {
				$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
			}
		} else {
			$message = Localization::getLocale('you_must_upload_a_valid_picture_before_you_can_submit_an_application');
		}
	} else {
		$message = Localization::getLocale('you_are_already_in_a_group');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: text/plain');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
?>
