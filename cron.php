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

require_once 'utils/utils.php';

// Only run this if we are running in a CLI enviroment.
if (Utils::isCli()) {
	require_once 'notificationmanager.php';
	require_once 'taskmanager.php';
	require_once 'handlers/eventhandler.php';
	require_once 'handlers/eventmigrationhandler.php';

	/* Event migration */
	$previousEvent = EventHandler::getPreviousEvent();
	$currentEvent = EventHandler::getCurrentEvent();

	// Check if we should automatically migrate from previous event,
	// this is done when booking time for the current event haven't happend yet, also that we're early in this event.
	if ($currentEvent->getBookingTime() >= time()) {
		// Migrates all information from the previous event to this one.
		EventMigrationHandler::copy($previousEvent, $currentEvent);
	}

	/* Automatic e-mail notifications */
	NotificationManager::checkForNotifications();

	/* Dynamic tasks */
	// Run all scheduled tasks.
	$taskList = TaskManager::getTasks();

	foreach ($taskList as $task) {
		$task->run();
	}
} else {
	echo Localization::getLocale("you_do_not_have_permission_to_do_this_you_will only be able to run this from command line");
}
?>
