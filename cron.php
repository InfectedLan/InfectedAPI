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

require_once 'utils/utils.php';

// Only run this if we are running in a CLI enviroment.
if (Utils::isCli()) {
	// Set the include path since we are running in CLI.
	require_once 'settings.php';
	set_include_path(Settings::api_path);

	require_once 'taskmanager.php';
	require_once 'handlers/eventmigrationhandler.php';
	require_once 'handlers/eventhandler.php';

	/* Static tasks */
	$event = EventHandler::getCurrentEvent();

	// Check if we should automatically migrate from previous event,
	// this is done when booking time for the current event haven't happend yet, also that we're early in this event.
	if ($event->getBookingTime() >= time()) {
		// Migrates all information from the previous event to this one.
		EventMigrationHandler::copy(EventHandler::getPreviousEvent(), $event);
	}

	/* Dynamic tasks */
	// Run all scheduled tasks.
	$taskList = TaskManager::getTasks();

	foreach ($taskList as $task) {
		$task->run();
	}
} else {
	echo 'You don\'t have permission do do this, you\'ll only be able to run this from the CLI.';
}
?>
