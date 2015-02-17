<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/group.php';
require_once 'objects/user.php';

class GroupHandler {
    /* 
     * Get a group by the internal id.
     */
    public static function getGroup($id) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
		
		    return $result->fetch_object('Group');
    }
    
    /* 
     * Get a group for the specified user.
     */
    public static function getGroupByUser(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '` 
                                    WHERE `id` = (SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '` 
                                                  WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
            								        AND `userId` = \'' . $user->getId() . '\');');
          
        $database->close();

        return $result->fetch_object('Group');
    }
    
    /*
     * Get a list of all groups.
     */
    public static function getGroups() {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '` 
                                    ORDER BY `name`;');
        
        $database->close();

        $groupList = array();
        
        while ($object = $result->fetch_object('Group')) {
            array_push($groupList, $object);
        }
        
        return $groupList;
    }
    
    /*
     * Create a new group
     */
    public static function createGroup(Event $event, $name, $title, $description, $leader, $coleader) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_crew_groups . '` (`eventId`, `name`, `title`, `description`, `leaderId`, `coleaderId`) 
                          VALUES (\'' . $event->getId() . '\', 
                                  \'' . $database->real_escape_string($name) . '\', 
                                  \'' . $database->real_escape_string($title) . '\', 
                                  \'' . $database->real_escape_string($description) . '\', 
                                  \'' . ($leader != null ? $leader->getId() : 0) . '\',
							                    \'' . ($coleader != null ? $coleader->getId() : 0) . '\');');
        
        $database->close();
    }
    
    /*
     * Update the specified group.
     */
    public static function updateGroup(Group $group, $name, $title, $description, $leader, $coleader) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('UPDATE `' . Settings::db_table_infected_crew_groups . '` 
          					      SET `name` = \'' . $database->real_escape_string($name) . '\', 
          						        `title` = \'' . $database->real_escape_string($title) . '\', 
          						        `description` = \'' . $database->real_escape_string($description) . '\', 
          						        `leaderId` = \'' . ($leader != null ? $leader->getId() : 0) . '\',
          						        `coleaderId` = \'' . ($coleader != null ? $coleader->getId() : 0) . '\'
          					      WHERE `id` = \'' . $group->getId() . '\';');
        
        $database->close();
    }
    
    /*
     * Remove the specified group
     */
    public static function removeGroup(Group $group) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_crew_groups . '` 
                          WHERE `id` = \'' . $group->getId() . '\';');
        
        $database->close();
    }
    
    /* 
	 * Returns an list of users that are members of this group.
	 */
    public static function getMembers(Group $group) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
                                    LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
                                    ON `' . Settings::db_table_infected_users . '`.`id` = `userId` 
                                    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								                    AND `groupId` = \'' . $group->getId() . '\'
                                    ORDER BY `firstname` ASC;');
        
        $database->close();

        $memberList = array();
        
        while ($object = $result->fetch_object('User')) {
            array_push($memberList, $object);
        }
        
        return $memberList;
    }
	
    /* 
     * Returns true of the specified user is member of a group.
     */
    public static function isGroupMember(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_memberof . '`
                                    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								                    AND `userId` = \'' . $user->getId() . '\'
                                    AND `groupId` != \'0\';');
        
		$database->close();
		
        return $result->num_rows > 0;
    }
    
    /* 
     * Return true if the specified user is leader of a group.
     */
    public static function isGroupLeader(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '` 
                                    WHERE `leaderId` = \'' . $user->getId() . '\';');
		$database->close();

         return $result->num_rows > 0;
    }
	
	/* 
     * Return true if user is co-leader for a group.
     */
    public static function isGroupCoLeader(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '` 
                                    WHERE `coleaderId` = \'' . $user->getId() . '\';');
        
        $database->close();

        return $result->num_rows > 0;
    }

    /*
     * Change the specifised users grooup to the one specified.
     */
    public static function changeGroupForUser(User $user, Group $group) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        if ($user->isGroupMember()) {    
            $database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '` 
                              SET `groupId` = \'' . $group->getId() . '\', 
                                  `teamId` = \'0\' 
                              WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
						                  AND `userId` = \'' . $user->getId() . '\';');
        } else {
            $database->query('INSERT INTO `' . Settings::db_table_infected_crew_memberof . '` (`eventId`, `userId`, `groupId`, `teamId`) 
                              VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\', 
								                      \'' . $user->getId() . '\', 
                                      \'' . $group->getId() . '\', 
                                      \'0\');');
        }
        
        $database->close();
    }
    
    /*
     * Remove a specified user from all groups.
     */
    public static function removeUserFromGroup(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_crew_memberof . '` 
                          WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
					                AND `userId` = \'' . $user->getId() . '\';');
        
        $database->close();
    }
}
?>