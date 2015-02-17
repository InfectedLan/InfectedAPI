<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/group.php';
require_once 'objects/user.php';

class GroupHandler {
    /* 
     * Get a group by the internal id.
     */
    public static function getGroup($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('Group');
    }
    
    /* 
     * Get a group for the specified user.
     */
    public static function getGroupForUser(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '` 
                                 WHERE `id` = (SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '` 
                                               WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
            								   AND `userId` = \'' . $user->getId() . '\');');
          
        $mysql->close();

        return $result->fetch_object('Group');
    }
    
    /*
     * Get a list of all groups.
     */
    public static function getGroups() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '` 
                                 ORDER BY `name`;');
        
        $mysql->close();

        $groupList = array();
        
        while ($object = $result->fetch_object('Group')) {
            array_push($groupList, $object);
        }
        
        return $groupList;
    }
    
    /*
     * Create a new group
     */
    public static function createGroup(Event $event, $name, $title, $description, User $leader, User $coleader) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_groups . '` (`eventId`, `name`, `title`, `description`, `leaderId`, `coleaderId`) 
                       VALUES (\'' . $event->getId() . '\', 
                               \'' . $mysql->real_escape_string($name) . '\', 
                               \'' . $mysql->real_escape_string($title) . '\', 
                               \'' . $mysql->real_escape_string($description) . '\', 
                               \'' . $leader->getId() . '\'
							   \'' . $coleader->getId() . '\');');
        
        $mysql->close();
    }
    
    /*
     * Update the specified group.
     */
    public static function updateGroup(Group $group, $name, $title, $description, User $leader, User $coleader) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_groups . '` 
					   SET `name` = \'' . $mysql->real_escape_string($name) . '\', 
						   `title` = \'' . $mysql->real_escape_string($title) . '\', 
						   `description` = \'' . $mysql->real_escape_string($description) . '\', 
						   `leader` = \'' . $leader->getId() . '\',
						   `coleader` = \'' . $coleader->getId() . '\'
					   WHERE `id` = \'' . $group->getId() . '\';');
        
        $mysql->close();
    }
    
    /*
     * Remove the specified group
     */
    public static function removeGroup(Group $group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_groups . '` 
                       WHERE `id` = \'' . $group->getId() . '\';');
        
        $mysql->close();
    }
    
    /* 
	 * Returns an list of users that are members of this group.
	 */
    public static function getMembers(Group $group) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
                                 LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
                                 ON `' . Settings::db_table_infected_users . '`.`id` = `userId` 
                                 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								 AND `groupId` = \'' . $group->getId() . '\'
                                 ORDER BY `firstname` ASC;');
        
        $mysql->close();

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
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_memberof . '`
                                 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								 AND `userId` = \'' . $user->getId() . '\'
                                 AND `groupId` != \'0\';');
        
		$mysql->close();
		
        return $result->num_rows > 0;
    }
    
    /* 
     * Return true if the specified user is leader of a group.
     */
    public static function isGroupLeader(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '` 
                                 WHERE `leader` = \'' . $user->getId() . '\';');
		$mysql->close();

         return $result->num_rows > 0;
    }
	
	/* 
     * Return true if user is co-leader for a group.
     */
    public static function isGroupCoLeader(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '` 
                                 WHERE `coleader` = \'' . $user->getId() . '\';');
        
        $mysql->close();

        return $result->num_rows > 0;
    }

    /*
     * Change the specifised users grooup to the one specified.
     */
    public static function changeGroupForUser(User $user, Group $group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        if ($user->isGroupMember()) {    
            $mysql->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '` 
                           SET `groupId` = \'' . $group->getId() . '\', 
                               `teamId` = \'0\' 
                           WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
						   AND `userId` = \'' . $user->getId() . '\';');
        } else {
            $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_memberof . '` (`eventId`, `userId`, `groupId`, `teamId`) 
                           VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\', 
								   \'' . $user->getId() . '\', 
                                   \'' . $group->getId() . '\', 
                                   \'0\');');
        }
        
        $mysql->close();
    }
    
    /*
     * Remove a specified user from all groups.
     */
    public static function removeUserFromGroup(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_memberof . '` 
                       WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
					   AND `userId` = \'' . $user->getId() . '\';');
        
        $mysql->close();
    }
}
?>