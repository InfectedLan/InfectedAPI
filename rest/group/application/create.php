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
require_once 'handlers/applicationhandler.php';
require_once 'handlers/grouphandler.php';

$result = false;
$status = http_response_code();
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	// Only allow non-members to apply.
	if (!$user->isGroupMember()) {
	    // Check that the user has an cropped avatar.
		if ($user->hasCroppedAvatar()) {
			if (isset($_POST['groupId']) &&
				isset($_POST['content']) &&
				is_numeric($_POST['groupId']) &&
				!empty($_POST['content'])) {
				$group = GroupHandler::getGroup($_POST['groupId']);
				$content = $_POST['content'];

				if ($group != null) {
				    if ($group->isActive()) {
                        if (!ApplicationHandler::hasUserApplicationsByGroup($user, $group)) {
                            ApplicationHandler::createApplication($group, $user, $content);
                            $result = true;
                            $status = 201; // Created.
                            $message = Localization::getLocale('your_appliction_to_value_is_now_submitted', $group->getTitle());
                        } else {
                            $message = Localization::getLocale('you_have_already_submitted_an_application_to_value_you_can_apply_again_if_your_application_should_be_denied', $group->getTitle());
                        }
                    } else {
                        $status = 404; // Not found.
                        $message = Localization::getLocale('this_group_is_deleted');
                    }
                } else {
                    $status = 404; // Not found.
                    $message = Localization::getLocale('this_group_does_not_exist');
                }
			} else {
                $status = 400; // Bad Request.
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

http_response_code($status);
header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();