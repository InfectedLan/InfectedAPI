<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'notificationmanager.php';
require_once 'objects/application.php';

class ApplicationHandler {
    /* 
     * Get an application by it's internal id (No matter event).
     */
    public static function getApplication($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_applications . '` 
                               WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                      
        $row = $result->fetch_array();
        
        $mysql->close();

        if ($row) {
            return new Application($row['id'], 
                                   $row['eventId'], 
                                   $row['groupId'],
                                   $row['userId'],                                    
                                   $row['openedTime'], 
                                   $row['closedTime'], 
                                   $row['state'], 
                                   $row['content'], 
                                   $row['comment']);
        }
    }
    
    /*
     * Returns a list of all applications (For all events)
     */
    public static function getApplications() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`;');
        
        $applicationList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($applicationList, self::getApplication($row['id']));
        }
        
        $mysql->close();
        
        return $applicationList;
    }
    
    /* 
     * Returns a list of pending applications.
     */
    public static function getPendingApplications() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
                                 LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
                                 ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
                                 WHERE `applicationId` IS NULL
                                 AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                 AND `state` = \'1\'
                                 ORDER BY `openedTime`;');
        
        $applicationList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($applicationList, self::getApplication($row['id']));
        }
        
        $mysql->close();
        
        return $applicationList;
    }
    
    /* 
     * Returns a list of pending applications.
     */
    public static function getPendingApplicationsForGroup($group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
                                 LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
                                 ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
                                 WHERE `applicationId` IS NULL
                                 AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                 AND `groupId` = \'' . $mysql->real_escape_string($group->getId()) .  '\'
                                 AND `state` = \'1\'
                                 ORDER BY `openedTime`;');
        
        $applicationList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($applicationList, self::getApplication($row['id']));
        }
        
        $mysql->close();
        
        return $applicationList;
    }
    
    /*
     * Returns a list of all queued applications.
     */
    public static function getQueuedApplications() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
                                 LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
                                 ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
                                 WHERE `applicationId` IS NOT NULL
                                 AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                 AND `state` = \'1\'
                                 ORDER BY `' . Settings::db_table_infected_crew_applicationqueue . '`.`id`;');
        
        $queuedApplicationList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($queuedApplicationList, self::getApplication($row['id']));
        }
        
        $mysql->close();
        
        return $queuedApplicationList;
    }
    
    /*
     * Returns a list of all queued applications for a given group.
     */
    public static function getQueuedApplicationsForGroup($group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `' . Settings::db_table_infected_crew_applications . '`.`id` FROM `' . Settings::db_table_infected_crew_applications . '`
                                 LEFT JOIN `' . Settings::db_table_infected_crew_applicationqueue . '`
                                 ON `' . Settings::db_table_infected_crew_applications . '`.`id` = `applicationId`
                                 WHERE `applicationId` IS NOT NULL
                                 AND `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                 AND `groupId` = \'' . $mysql->real_escape_string($group->getId()) .  '\'
                                 AND `state` = \'1\'
                                 ORDER BY `' . Settings::db_table_infected_crew_applicationqueue . '`.`id`;');
        
        $queuedApplicationList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($queuedApplicationList, self::getApplication($row['id']));
        }
        
        $mysql->close();
        
        return $queuedApplicationList;
    }
    
    /* 
     * Create a new application. 
     */
    public static function createApplication($group, $user, $mysqltent) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_applications . '` (`eventId`, `groupId`, `userId`, `openedTime`, `state`, `content`) 
                       VALUES (\'' . EventHandler::getCurrentEvent() . '\', 
                             \'' . $mysql->real_escape_string($group->getId()) . '\', 
                             \'' . $mysql->real_escape_string($user->getId()) . '\', 
                             \'' . date('Y-m-d H:i:s') . '\',
                             \'1\',
                             \'' . $mysql->real_escape_string($mysqltent) . '\');');
        
        $mysql->close();
        
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
    public static function removeApplication($application) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        // Remove the application.
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_applications . '` 
                       WHERE `id` = \'' . $mysql->real_escape_string($application->getId()) . '\';');
        
        $mysql->close();
        
        // Remove the application from the queue, if present.
        self::unqueueApplication($application);
    }
    
    /*
     * Accepts an application, with a optional comment.
     */
    public static function acceptApplication($application, $comment, $notify) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_applications . '` 
                       SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
                           `state` = \'2\',
                           `comment` = \'' . $mysql->real_escape_string($comment) . '\'
                       WHERE `id` = \'' . $mysql->real_escape_string($application->getId()) . '\';');
        
        $mysql->close();
        
        $user = $application->getUser();
        $group = $application->getGroup();
        
        // Remove the application from the queue, if present.
        self::unqueueApplication($application);
        
        // Reject users application for all other groups.
        $applicationList = self::getUserApplications($user);
        
        foreach ($applicationList as $value) {
            if ($group->getId() != $value->getGroup()->getId()) {
                self::closeApplication($value);
            }
        }
        
        // Set the user in the new group
        GroupHandler::changeGroupForUser($user, $group);
        
        // Notify the user by email, if notify is true.
        if ($notify) {
            // Send email notification to the user.
            NotificationManager::sendApplicationAccpetedNotification($application);
        }
    }
    
    /*
     * Rejects an application, with a optional comment.
     */
    public static function rejectApplication($application, $comment, $notify) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_applications . '` 
                       SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
                           `state` = \'3\', 
                           `comment` = \'' . $mysql->real_escape_string($comment) . '\'
                       WHERE `id` = \'' . $mysql->real_escape_string($application->getId()) . '\';');
        
        $mysql->close();
        
        // Remove the application from the queue, if present.
        self::unqueueApplication($application);
        
        // Notify the user by email, if notify is true.
        if ($notify) {
            NotificationManager::sendApplicationRejectedNotification($application, $comment);
        }
    }
    
    /*
     * Rejects an application, with a optional comment.
     */
    public static function closeApplication($application) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_applications . '` 
                       SET `closedTime` = \'' . date('Y-m-d H:i:s') . '\',
                           `state` = \'4\',
                           `comment` = \'Closed by the system.\'
                       WHERE `id` = \'' . $mysql->real_escape_string($application->getId()) . '\';');
        
        $mysql->close();
        
        // Remove the application from the queue, if present.
        self::unqueueApplication($application);
    }
    
    /*
     * Checks if an application is queued.
     */
    public static function isQueued($application) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applicationqueue . '` 
                                 WHERE `applicationId` = \'' . $mysql->real_escape_string($application->getId()) . '\';');
        
        $row = $result->fetch_array();
        
        $mysql->close();
        
        return $row ? true : false;
    }
    
    /*
     * Puts an application in queue.
     */
    public static function queueApplication($application, $notify) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        if (!self::isQueued($application)) {
            $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_applicationqueue . '` (`applicationId`) 
                         VALUES (\'' . $mysql->real_escape_string($application->getId()) . '\');');
        }
                                    
        $mysql->close();
        
        // Notify the user by email, if notify is true.
        if ($notify) {
            NotificationManager::sendApplicationQueuedNotification($application);
        }
    }
    
    /*
     * Removes an application from queue.
     */
    public static function unqueueApplication($application) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_applicationqueue . '` 
                     WHERE `applicationId` = \'' . $mysql->real_escape_string($application->getId()) . '\';');
                                    
        $mysql->close();
    }
    
    /*
     * Returns a true if user has application for group.
     */
    public static function hasUserApplicationForGroup($user, $group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
                               WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                               AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                               AND `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\'
                               AND `state` = \'1\'
                               OR `state` = \'2\';');
        
        $row = $result->fetch_array();
        
        $mysql->close();
        
        return $row ? true : false;
    }
    
    /*
     * Returns the application for group and user.
     */
    public static function getUserApplicationForGroup($user, $group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
                               WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                               AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                               AND `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\'
                               AND `state` = \'1\'
                               OR `state` = \'2\';');
        
        $row = $result->fetch_array();
        
        $mysql->close();
        
        return self::getApplication($row['id']);
    }
    
    /*
     * Returns a list of all applications for given user.
     */
    public static function getUserApplications($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
                               WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                               AND `userId` = \'' . $user->getId() . '\';');
        
        $applicationList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($applicationList, self::getApplication($row['id']));
        }
        
        $mysql->close();
        
        return $applicationList;
    }
    
    /*
     * Returns a list of all applications for that event.
     */
    public static function getApplicationsForEvent($event) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_applications . '`
                               WHERE `eventId` = \'' . $event->getId() . '\';');
        
        $applicationList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($applicationList, self::getApplication($row['id']));
        }
        
        $mysql->close();
        
        return $applicationList;
    }
}
?>