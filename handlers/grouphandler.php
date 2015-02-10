<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/group.php';
require_once 'objects/user.php';

class GroupHandler {
    /* 
     * Get a group by the internal id
     */
    public static function getGroup($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();

        if ($row) {
            return new Group($row['id'], 
                             $row['name'], 
                             $row['title'], 
                             $row['description'], 
                             $row['leader'],
							 $row['coleader'],
                             $row['queuing']);
        }
    }
    
    /* 
     * Get a group for the specified user.
     */
    public static function getGroupForUser($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '` 
                                 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								 AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();
        
        if ($row) {
            return self::getGroup($row['groupId']);
        }
    }
    
    /*
     * Get a list of all groups.
     */
    public static function getGroups() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '` 
                                 ORDER BY `name`;');
        
        $mysql->close();

        $groupList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($groupList, self::getGroup($row['id']));
        }
        
        return $groupList;
    }
    
    /*
     * Create a new group
     */
    public static function createGroup($name, $title, $description, $leader, $coleader) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_groups . '` (`name`, `title`, `description`, `leader`, `coleader`) 
                       VALUES (\'' . $mysql->real_escape_string($name) . '\', 
                               \'' . $mysql->real_escape_string($title) . '\', 
                               \'' . $mysql->real_escape_string($description) . '\', 
                               \'' . $mysql->real_escape_string($leader) . '\'
							   \'' . $mysql->real_escape_string($coleader) . '\');');
        
        $mysql->close();
    }
    
    /*
     * Update the specified group.
     */
    public static function updateGroup($id, $name, $title, $description, $leader, $coleader) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_groups . '` 
					   SET `name` = \'' . $mysql->real_escape_string($name) . '\', 
						   `title` = \'' . $mysql->real_escape_string($title) . '\', 
						   `description` = \'' . $mysql->real_escape_string($description) . '\', 
						   `leader` = \'' . $mysql->real_escape_string($leader) . '\',
						   `coleader` = \'' . $mysql->real_escape_string($coleader) . '\'
					   WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
    
    /*
     * Remove the specified group
     */
    public static function removeGroup($group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_groups . '` 
                       WHERE `id` = \'' . $mysql->real_escape_string($group->getId()) . '\';');
        
        $mysql->close();
    }
    
    /* 
	 * Returns an list of users that are members of this group.
	 */
    public static function getMembers($group) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `' . Settings::db_table_infected_users . '`.`id` FROM `' . Settings::db_table_infected_users . '`
                                 LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
                                 ON `' . Settings::db_table_infected_users . '`.`id` = `userId` 
                                 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								 AND `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\'
                                 ORDER BY `firstname` ASC;');
        
        $mysql->close();

        $memberList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($memberList, UserHandler::getUser($row['id']));
        }
        
        return $memberList;
    }
	
    /* 
     * Returns true of the specified user is member of a group.
     */
    public static function isGroupMember($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '`
                                 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								 AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
                                 AND `groupId` != \'0\';');
        
		$mysql->close();
		
        $row = $result->fetch_array();
        
        return $row ? true : false;
    }
    
    /* 
     * Return true if the specified user is leader of a group.
     */
    public static function isGroupLeader($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `leader` 
                                 FROM `' . Settings::db_table_infected_crew_groups . '` 
                                 WHERE `leader` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
		$mysql->close();

        $row = $result->fetch_array();
        
        return $row ? true : false;
    }
	
	/* 
     * Return true if user is co-leader for a group.
     */
    public static function isGroupCoLeader($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `coleader` 
                                 FROM `' . Settings::db_table_infected_crew_groups . '` 
                                 WHERE `coleader` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();
        
        return $row ? true : false;
    }

    /*
     * Change the specifised users grooup to the one specified.
     */
    public static function changeGroupForUser($user, $group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        if ($user->isGroupMember()) {    
            $mysql->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '` 
                           SET `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\', 
                               `teamId` = \'0\' 
                           WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
						   AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        } else {
            $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_memberof . '` (`eventId`, `userId`, `groupId`, `teamId`) 
                           VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\', 
								   \'' . $mysql->real_escape_string($user->getId()) . '\', 
                                   \'' . $mysql->real_escape_string($group->getId()) . '\', 
                                   \'0\');');
        }
        
        $mysql->close();
    }
    
    /*
     * Remove a specified user from all groups.
     */
    public static function removeUserFromGroup($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_memberof . '` 
                       WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
					   AND `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
    }
}
?>