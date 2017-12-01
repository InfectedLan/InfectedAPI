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

require_once 'settings.php';
require_once 'database.php';
require_once 'notificationmanager.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/application.php';
require_once 'objects/group.php';
require_once 'objects/user.php';
require_once 'objects/event.php';

class ApplicationHandler {
  const STATUS_NEW = 1;
  const STATUS_ACCEPTED = 2;
  const STATUS_REJECTED = 3;
  const STATUS_CLOSED = 4;

	/*
	 * Get an application by the internal id.
	 */
	public static function getApplication(int $id): Application {
    $database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Application');
	}

	/*
	 * Returns a list of all applications for that event.
	 */
	public static function getApplications(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																ORDER BY `openedTime`;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Returns a list of pending applications.
	 */
	public static function getPendingApplications(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.* FROM `' . Settings::db_table_infected_crew_applications . '`
																LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
																ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
																WHERE `applicationId` IS NULL
																AND `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `state` = \'1\'
																ORDER BY `openedTime`;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Returns a list of pending applications.
	 */
	public static function getPendingApplicationsByGroup(Group $group, Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.* FROM `' . Settings::db_table_infected_crew_applications . '`
																LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
																ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
																WHERE `applicationId` IS NULL
																AND `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `groupId` = \'' . $group->getId() .  '\'
																AND `state` = \'1\'
																ORDER BY `openedTime`;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Returns a list of all queued applications.
	 */
	public static function getQueuedApplications(Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.* FROM `' . Settings::db_table_infected_crew_applications . '`
																LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
																ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
																WHERE `applicationId` IS NOT NULL
																AND `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `state` = \'1\'
																ORDER BY `' . Settings::db_table_infected_crew_applicationqueue . '`.`id`;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Returns a list of all queued applications for a given group.
	 */
	public static function getQueuedApplicationsByGroup(Group $group, Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.* FROM `' . Settings::db_table_infected_crew_applications . '`
																LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
																ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
																WHERE `applicationId` IS NOT NULL
																AND `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `groupId` = \'' . $group->getId() .  '\'
																AND `state` = \'1\'
																ORDER BY `' . Settings::db_table_infected_crew_applicationqueue . '`.`id`;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Returns a list of all accepted applications.
	 */
	public static function getAcceptedApplications(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `state` = \'2\'
																ORDER BY `openedTime` DESC;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Returns a list of all accepted applications for a given group.
	 */
	public static function getAcceptedApplicationsByGroup(Group $group, Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
                                AND `groupId` = \'' . $group->getId() .  '\'
																AND `state` = \'2\'
																ORDER BY `openedTime` DESC;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Returns a list of all rejected applications.
	 */
	public static function getRejectedApplications(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
                                AND `state` = \'3\'
																ORDER BY `openedTime` DESC;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Returns a list of all rejected applications for a given group.
	 */
	public static function getRejectedApplicationsByGroup(Group $group, Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
                                AND `groupId` = \'' . $group->getId() .  '\'
																AND `state` = \'3\'
																ORDER BY `openedTime` DESC;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Returns a list of all previous applications.
	 */
	public static function getPreviousApplications(Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
                                AND (`state` = \'2\' OR `state` = \'3\')
																ORDER BY `closedTime` DESC, `openedTime` DESC;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Returns a list of all previous applications for a given group.
	 */
	public static function getPreviousApplicationsByGroup(Group $group, Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
                                AND `groupId` = \'' . $group->getId() .  '\'
																AND (`state` = \'2\' OR `state` = \'3\')
																ORDER BY `closedTime` DESC, `openedTime` DESC;');

		$applicationList = [];

		while ($object = $result->fetch_object('Application')) {
			$applicationList[] = $object;
		}

		return $applicationList;
	}

	/*
	 * Create a new application.
	 */
	public static function createApplication(Group $group, User $user, Event $event = null, string $content): Application {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('INSERT INTO `' . Settings::db_table_infected_crew_applications . '` (`eventId`, `groupId`, `userId`, `openedTime`, `closedTime`, `state`, `content`, `updatedByUserId`, `comment`)
										  VALUES (\'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\',
														  \'' . $group->getId() . '\',
														  \'' . $user->getId() . '\',
														  \'' . date('Y-m-d H:i:s') . '\',
                              \'NULL\',
														  \'1\',
														  \'' . $database->real_escape_string($content) . '\',
                              \'0\',
                              \'\');');

		$application = self::getApplication($database->insert_id);

		// If the group is set to queue applications, do so automatically.
		if ($group->isQueuing()) {
			self::queueApplication($application, Session::getCurrentUser());
		}

		// Notify the group leader by email.
		NotificationManager::sendApplicationCreatedNotification($application);

		return $application;
	}

	/*
	 * Remove an application.
	 */
	public static function removeApplication(Application $application) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		// Remove the application.
		$database->query('DELETE FROM `' . Settings::db_table_infected_crew_applications . '`
						  				WHERE `id` = \'' . $application->getId() . '\';');

		// Remove the application from the queue, if present.
		self::unqueueApplication($application, Session::getCurrentUser());
	}

	/*
	 * Accepts an application, with a optional comment.
	 */
	public static function acceptApplication(Application $application, User $user, string $comment, bool $notify) {
		// Only allow application for current event to be accepted.
		if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
			$database = Database::getConnection(Settings::db_name_infected_crew);

			$database->query('UPDATE `' . Settings::db_table_infected_crew_applications . '`
											  SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
														`state` = \'2\',
														`updatedByUserId` = \'' . $user->getId() . '\',
														`comment` = \'' . $database->real_escape_string($comment) . '\'
											  WHERE `id` = \'' . $application->getId() . '\';');

			$applicationUser = $application->getUser();
			$group = $application->getGroup();

			// Remove the application from the queue, if present.
			self::unqueueApplication($application, $user);

			// Reject users application for all other groups.
			$applicationList = self::getUserApplications($applicationUser);

			foreach ($applicationList as $applicationValue) {
				if (!$group->equals($applicationValue->getGroup())) {
					self::closeApplication($applicationValue, $user);
				}
			}

			// Set the user in the new group
			GroupHandler::changeGroupForUser($applicationUser, $group);

			// Notify the user by email, if notify is true.
			if ($notify) {
				// Send email notification to the user.
				NotificationManager::sendApplicationAccpetedNotification($application);
			}
		}
	}

	/*
	 * Rejects an application, with a optional comment.
	 */
	public static function rejectApplication(Application $application, User $user, string $comment, bool $notify) {
		// Only allow application for current event to be rejected.
		if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
			$database = Database::getConnection(Settings::db_name_infected_crew);

			$database->query('UPDATE `' . Settings::db_table_infected_crew_applications . '`
											  SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
													  `state` = \'3\',
													  `updatedByUserId` = \'' . $user->getId() . '\',
													  `comment` = \'' . $database->real_escape_string($comment) . '\'
											  WHERE `id` = \'' . $application->getId() . '\';');

			// Remove the application from the queue, if present.
			self::unqueueApplication($application, $user);

			// Notify the user by email, if notify is true.
			if ($notify) {
				NotificationManager::sendApplicationRejectedNotification($application, $comment);
			}
		}
	}

	/*
	 * Closes an application, should be used instead of removal for history.
	 */
	public static function closeApplication(Application $application, User $user) {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$database->query('UPDATE `' . Settings::db_table_infected_crew_applications . '`
										  SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
											  	`state` = \'4\',
													`updatedByUserId` = \'' . $user->getId() . '\',
											  	`comment` = \'Closed by the system.\'
										  WHERE `id` = \'' . $application->getId() . '\';');

		// Remove the application from the queue, if present.
		self::unqueueApplication($application, $user);
	}

	/*
	 * Puts an application in queue.
	 */
	public static function queueApplication(Application $application, User $user, bool $notify) {
		// Only allow application for current event to be queued.
		if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
  			if (!self::isQueued($application)) {
					$database = Database::getConnection(Settings::db_name_infected_crew);

					$database->query('INSERT INTO `' . Settings::db_table_infected_crew_applicationqueue . '` (`applicationId`)
							 		  				VALUES (\'' . $application->getId() . '\');');

					$database->query('UPDATE `' . Settings::db_table_infected_crew_applications . '`
		  							  			SET `updatedByUserId` = \'' . $user->getId() . '\'
		  							  			WHERE `id` = \'' . $application->getId() . '\';');
  			}

  			// Notify the user by email, if notify is true.
  			if ($notify) {
  				  NotificationManager::sendApplicationQueuedNotification($application);
  			}
		}
	}

	/*
	 * Removes an application from queue.
	 */
	public static function unqueueApplication(Application $application, User $user) {
		// Only allow application for current event to be unqueued.
		if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
			$database = Database::getConnection(Settings::db_name_infected_crew);

			$database->query('DELETE FROM `' . Settings::db_table_infected_crew_applicationqueue . '`
							  				WHERE `applicationId` = \'' . $application->getId() . '\';');

			$database->query('UPDATE `' . Settings::db_table_infected_crew_applications . '`
			  							  SET `updatedByUserId` = \'' . $user->getId() . '\'
			  							  WHERE `id` = \'' . $application->getId() . '\';');
		}
	}

	/*
	 * Checks if an application is queued.
	 */
	public static function isQueued(Application $application): bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applicationqueue . '`
																WHERE `applicationId` = \'' . $application->getId() . '\';');

		return $result->num_rows > 0;
	}

  /*
   * Returns a list of all applications for given user.
   */
  public static function getUserApplications(User $user, Event $event = null): array {
    $database = Database::getConnection(Settings::db_name_infected_crew);

    $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
                                AND `userId` = \'' . $user->getId() . '\';');

    $applicationList = [];

    while ($object = $result->fetch_object('Application')) {
      $applicationList[] = $object;
    }

    return $applicationList;
  }

	/*
	 * Returns a true if user has application for group.
	 */
	public static function hasUserApplicationsByGroup(User $user, Group $group, Event $event = null): bool {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `userId` = \'' . $user->getId() . '\'
																AND `groupId` = \'' . $group->getId() . '\'
																AND (`state` = \'1\' OR `state` = \'2\');');

		return $result->num_rows > 0;
	}

	/*
	 * Returns the application for user, group and event.
	 */
	public static function getUserApplicationsByGroup(User $user, Group $group, Event $event = null): array {
		$database = Database::getConnection(Settings::db_name_infected_crew);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
																WHERE `eventId` = \'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\'
																AND `userId` = \'' . $user->getId() . '\'
																AND `groupId` = \'' . $group->getId() . '\'
																AND (`state` = \'1\' OR `state` = \'2\');');

		return $result->fetch_object('Application');
	}
}
?>
