<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'notificationmanager.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/application.php';
require_once 'objects/group.php';
require_once 'objects/user.php';
require_once 'objects/event.php';

class ApplicationHandler {
    /* 
     * Get an application by the internal id.
     */
    public static function getApplication($id) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
		
		return $result->fetch_object('Application');
    }
    
    /*
     * Returns a list of all applications (For all events)
     */
    public static function getApplications() {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`;');
        
        $database->close();

        $applicationList = array();
        
        while ($object = $result->fetch_object('Application')) {
            array_push($applicationList, $object);
        }
        
        return $applicationList;
    }
    
    /* 
     * Returns a list of pending applications.
     */
    public static function getPendingApplications() {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.* FROM `' . Settings::db_table_infected_crew_applications . '`
                                    LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
                                    ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
                                    WHERE `applicationId` IS NULL
                                    AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                    AND `state` = \'1\'
                                    ORDER BY `openedTime`;');
        
        $database->close();

        $applicationList = array();
        
        while ($object = $result->fetch_object('Application')) {
            array_push($applicationList, $object);
        }
        
        return $applicationList;
    }
    
    /* 
     * Returns a list of pending applications.
     */
    public static function getPendingApplicationsForGroup(Group $group) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.* FROM `' . Settings::db_table_infected_crew_applications . '`
                                    LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
                                    ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
                                    WHERE `applicationId` IS NULL
                                    AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                    AND `groupId` = \'' . $group->getId() .  '\'
                                    AND `state` = \'1\'
                                    ORDER BY `openedTime`;');
        
        $database->close();

        $applicationList = array();
        
        while ($object = $result->fetch_object('Application')) {
            array_push($applicationList, $object);
        }

        return $applicationList;
    }
    
    /*
     * Returns a list of all queued applications.
     */
    public static function getQueuedApplications() {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.* FROM `' . Settings::db_table_infected_crew_applications . '`
                                    LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
                                    ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
                                    WHERE `applicationId` IS NOT NULL
                                    AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                    AND `state` = \'1\'
                                    ORDER BY `' . Settings::db_table_infected_crew_applicationqueue . '`.`id`;');
        
        $database->close();

        $applicationList = array();

        while ($object = $result->fetch_object('Application')) {
            array_push($applicationList, $object);
        }
        
        return $applicationList;
    }
    
    /*
     * Returns a list of all queued applications for a given group.
     */
    public static function getQueuedApplicationsForGroup(Group $group) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.* FROM `' . Settings::db_table_infected_crew_applications . '`
                                    LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
                                    ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
                                    WHERE `applicationId` IS NOT NULL
                                    AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                    AND `groupId` = \'' . $group->getId() .  '\'
                                    AND `state` = \'1\'
                                    ORDER BY `' . Settings::db_table_infected_crew_applicationqueue . '`.`id`;');
        
        $database->close();

        $applicationList = array();
        
        while ($object = $result->fetch_object('Application')) {
            array_push($applicationList, $object);
        }
        
        return $applicationList;
    }
    
	/*
     * Returns a list of all accepted applications.
     */
    public static function getAcceptedApplications() {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                    WHERE `state` = \'2\'
								    ORDER BY `openedTime` DESC;');
        
		$database->close();
		
        $acceptedApplicationList = array();
        
        while ($object = $result->fetch_object('Application')) {
            array_push($acceptedApplicationList, $object);
        }
        
        return $acceptedApplicationList;
    }
    
    /*
     * Returns a list of all accepted applications for a given group.
     */
    public static function getAcceptedApplicationsForGroup(Group $group) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                    WHERE `groupId` = \'' . $group->getId() .  '\'
								    AND `state` = \'2\'
                                    ORDER BY `openedTime` DESC;');
        
		$database->close();
		
        $acceptedApplicationList = array();
        
        while ($object = $result->fetch_object('Application')) {
            array_push($acceptedApplicationList, $object);
        }
        
        return $acceptedApplicationList;
    }
	
    /* 
     * Create a new application. 
     */
    public static function createApplication(Group $group, User $user, $content) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_crew_applications . '` (`eventId`, `groupId`, `userId`, `openedTime`, `state`, `content`) 
                          VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\', 
                                  \'' . $group->getId() . '\', 
                                  \'' . $user->getId() . '\', 
                                  \'' . date('Y-m-d H:i:s') . '\',
                                  \'1\',
                                  \'' . $database->real_escape_string($content) . '\');');
        
        $database->close();
        
        // If the group is set to queue applications, do so automatically.
        if ($group->isQueuing()) {
            $application = self::getUserApplicationForGroup($user, $group);
        
            self::queueApplication($application);
        }
        
        // Notify the group leader by email.
        NotificationManager::sendApplicationCreatedNotification($user, $group);
    }
    
    /* 
     * Remove an application.
     */
    public static function removeApplication(Application $application) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        // Remove the application.
        $database->query('DELETE FROM `' . Settings::db_table_infected_crew_applications . '` 
                          WHERE `id` = \'' . $application->getId() . '\';');
        
        $database->close();
        
        // Remove the application from the queue, if present.
        self::unqueueApplication($application);
    }
    
    /*
     * Accepts an application, with a optional comment.
     */
    public static function acceptApplication(User $user, Application $application, $comment, $notify) {
		// Only allow application for current event to be accepted.
		if ($applicatin->getEvent()->equals(EventHandler::getCurrentEvent())) {
			$database = Database::open(Settings::db_name_infected_crew);
			
			$database->query('UPDATE `' . Settings::db_table_infected_crew_applications . '` 
						      SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
							      `state` = \'2\',
							      `updatedByUserId` = \'' . $user->getId() . '\',
							      `comment` = \'' . $database->real_escape_string($comment) . '\'
						      WHERE `id` = \'' . $application->getId() . '\';');
			
			$database->close();
			
			$applicationUser = $application->getUser();
			$group = $application->getGroup();
			
			// Remove the application from the queue, if present.
			self::unqueueApplication($user, $application);
			
			// Reject users application for all other groups.
			$applicationList = self::getUserApplications($applicationUser);
			
			foreach ($applicationList as $applicationValue) {
                if ($group->equals($applicationValue->getGroup())) {
					self::closeApplication($user, $applicationValue);
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
    public static function rejectApplication(User $user, Application $application, $comment, $notify) {
		// Only allow application for current event to be rejected.
        if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
			$database = Database::open(Settings::db_name_infected_crew);
			
			$database->query('UPDATE `' . Settings::db_table_infected_crew_applications . '` 
						      SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
							      `state` = \'3\', 
							      `updatedByUserId` = \'' . $user->getId() . '\',
							      `comment` = \'' . $database->real_escape_string($comment) . '\'
						      WHERE `id` = \'' . $application->getId() . '\';');
			
			$database->close();
			
			// Remove the application from the queue, if present.
			self::unqueueApplication($user, $application);
			
			// Notify the user by email, if notify is true.
			if ($notify) {
				NotificationManager::sendApplicationRejectedNotification($application, $comment);
			}
		}
    }
    
    /*
     * Rejects an application, with a optional comment.
     */
    public static function closeApplication(User $user, Application $application) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('UPDATE `' . Settings::db_table_infected_crew_applications . '` 
                          SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
                              `state` = \'4\',
					    	  `updatedByUserId` = \'' . $user->getId() . '\',
                              `comment` = \'Closed by the system.\'
                          WHERE `id` = \'' . $application->getId() . '\';');
        
        $database->close();
        
        // Remove the application from the queue, if present.
        self::unqueueApplication($user, $application);
    }
    
    /*
     * Checks if an application is queued.
     */
    public static function isQueued(Application $application) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applicationqueue . '` 
                                    WHERE `applicationId` = \'' . $application->getId() . '\';');
        

        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Puts an application in queue.
     */
    public static function queueApplication(User $user, Application $application, $notify) {
		// Only allow application for current event to be queued.
        if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
			if (!self::isQueued($application)) {
				$database = Database::open(Settings::db_name_infected_crew);
				
				$database->query('INSERT INTO `' . Settings::db_table_infected_crew_applicationqueue . '` (`applicationId`) 
							      VALUES (\'' . $application->getId() . '\');');
							   
				$database->query('UPDATE `' . Settings::db_table_infected_crew_applications . '` 
							      SET `updatedByUserId` = \'' . $user->getId() . '\'
							      WHERE `id` = \'' . $application->getId() . '\';');
							   
				$database->close();
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
    public static function unqueueApplication(User $user, Application $application) {
		// Only allow application for current event to be unqueued.
        if ($application->getEvent()->equals(EventHandler::getCurrentEvent())) {
			$database = Database::open(Settings::db_name_infected_crew);
			
			$database->query('DELETE FROM `' . Settings::db_table_infected_crew_applicationqueue . '` 
						      WHERE `applicationId` = \'' . $application->getId() . '\';');
						   
			$database->query('UPDATE `' . Settings::db_table_infected_crew_applications . '` 
			                  SET `updatedByUserId` = \'' . $user->getId() . '\'
			                  WHERE `id` = \'' . $application->getId() . '\';');
                                    
			$database->close();
		}
    }
    
    /*
     * Returns a true if user has application for group.
     */
    public static function hasUserApplicationForGroup(User $user, Group $group) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
                                    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                    AND `userId` = \'' . $user->getId() . '\'
                                    AND `groupId` = \'' . $group->getId() . '\'
                                    AND (`state` = \'1\' OR `state` = \'2\');');
        
        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Returns the application for group and user.
     */
    public static function getUserApplicationForGroup(User $user, Group $group) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                    AND `userId` = \'' . $user->getId() . '\'
                                    AND `groupId` = \'' . $group->getId() . '\'
                                    AND (`state` = \'1\' OR `state` = \'2\');');
        
        $database->close();
        
        return $result->fetch_object('Application');
    }
    
    /*
     * Returns a list of all applications for given user.
     */
    public static function getUserApplications(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                    AND `userId` = \'' . $user->getId() . '\';');
        
        $database->close();

        $applicationList = array();
        
        while ($object = $result->fetch_object('Application')) {
            array_push($applicationList, $object);
        }
        
        return $applicationList;
    }
    
    /*
     * Returns a list of all applications for that event.
     */
    public static function getApplicationsForEvent(Event $event) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '`
                                    WHERE `eventId` = \'' . $event->getId() . '\';');
        
        $database->close();

        $applicationList = array();
        
        while ($object = $result->fetch_object('Application')) {
            array_push($applicationList, $object);
        }
        
        return $applicationList;
    }
}
?>