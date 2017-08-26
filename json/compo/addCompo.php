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
require_once 'handlers/compohandler.php';

$result = false;
$message = null;
$id = 0;

if (Session::isAuthenticated()) {
    $user = Session::getCurrentUser();

    if ($user->hasPermission('event.compo')) {
	if (isset($_GET['title']) &&
	    isset($_GET['tag']) &&
	    isset($_GET['startTime']) &&
	    isset($_GET['startDate']) &&
	    isset($_GET['registrationEndTime']) &&
	    isset($_GET['registrationEndDate']) &&
	    isset($_GET['compoPlugin']) &&
	    isset($_GET['teamSize']) &&
	    isset($_GET['maxTeamCount']) &&
	    !empty($_GET['title']) &&
	    !empty($_GET['tag']) &&
	    !empty($_GET['startTime']) &&
	    !empty($_GET['startDate']) &&
	    !empty($_GET['registrationEndTime']) &&
	    !empty($_GET['registrationEndDate']) &&
	    is_numeric($_GET['teamSize']) &&
	    is_numeric($_GET['maxTeamCount'])) {
	    $name = strtolower(str_replace(' ', '-', $_GET['title']));
	    $title = $_GET['title'];
	    $tag = $_GET['tag'];
            $compoPlugin = $_GET['compoPlugin'];
	    $description = $_GET['description'];
	    $mode = $_GET['mode'];
	    $startTime = $_GET['startDate'] . ' ' . $_GET['startTime'];
	    $registrationEndTime = $_GET['registrationEndDate'] . ' ' . $_GET['registrationEndTime'];
	    $teamSize = $_GET['teamSize'];
            $maxTeamCount = $_GET['maxTeamCount'];

	    $id = CompoHandler::createCompo($name, $title, $tag, $description, $compoPlugin, $startTime, $registrationEndTime, $teamSize, $maxTeamCount);
            
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
echo json_encode(['result' => $result, 'message' => $message, 'id' => $id], JSON_PRETTY_PRINT);
Database::cleanup();
?>
