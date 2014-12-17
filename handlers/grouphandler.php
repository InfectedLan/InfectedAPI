<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/group.php';
require_once 'objects/user.php';

class GroupHandler {
    /* Get a group by id */
    public static function getGroup($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_groups . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                    
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        if ($row) {
            return new Group($row['id'], 
                             $row['name'], 
                             $row['title'], 
                             $row['description'], 
                             $row['leader'],
                             $row['queuing']);
        }
    }
    
    /* Get a group by userId */
    public static function getGroupForUser($userId) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `groupId` FROM `' . Settings::db_table_infected_crew_memberof . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($userId) . '\';');
                                    
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        if ($row) {
            return self::getGroup($row['groupId']);
        }
    }
    
    /* Get an array of all groups */
    public static function getGroups() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_groups . '` 
                                      ORDER BY `name`;');
        
        $groupList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($groupList, self::getGroup($row['id']));
        }
        
        $mysql->close();
        
        return $groupList;
    }
    
    /* Create a new group */
    public static function createGroup($name, $title, $description, $leader) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_groups . '` (`name`, `title`, `description`, `leader`) 
                            VALUES (\'' . $mysql->real_escape_string($name) . '\', 
                                    \'' . $mysql->real_escape_string($title) . '\', 
                                    \'' . $mysql->real_escape_string($description) . '\', 
                                    \'' . $mysql->real_escape_string($leader) . '\');');
        
        $mysql->close();
    }
    
    /* Update a page */
    public static function updateGroup($id, $name, $title, $description, $leader) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_groups . '` 
                            SET `name` = \'' . $mysql->real_escape_string($name) . '\', 
                                `title` = \'' . $mysql->real_escape_string($title) . '\', 
                                `description` = \'' . $mysql->real_escape_string($description) . '\', 
                                `leader` = \'' . $mysql->real_escape_string($leader) . '\'
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
    
    /* Remove a page */
    public static function removeGroup($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_groups . '` 
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
    
    /* Returns an array of users that are members of this group */
    public static function getMembers($groupId) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `' . Settings::db_table_infected_users . '`.`id` FROM `' . Settings::db_table_infected_users . '`
                                      LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
                                      ON `' . Settings::db_table_infected_users . '`.`id` = `userId` 
                                      WHERE `groupId` = \'' . $mysql->real_escape_string($groupId) . '\'
                                      ORDER BY `firstname` ASC;');
        
        $memberList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($memberList, UserHandler::getUser($row['id']));
        }
        
        $mysql->close();
        
        return $memberList;
    }

    /* 
     * Is member of a group which means it's not a member user.
     */
    public static function isGroupMember($userId) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `groupId` 
                                      FROM `' . Settings::db_table_infected_crew_memberof . '`
                                      WHERE `userId` = \'' . $mysql->real_escape_string($userId) . '\'
                                      AND `groupId` != \'0\';');
                                      
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        return $row ? true : false;
    }
    
    /* 
     * Return true if user is leader for a group.
     */
    public static function isGroupLeader($userId) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `leader` 
                                      FROM `' . Settings::db_table_infected_crew_groups . '` 
                                      WHERE `leader` = \'' . $mysql->real_escape_string($userId) . '\';');
                                      
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        return $row ? true : false;
    }

    /*
     * Sets the users group.
     */
    public static function changeGroupForUser($user, $group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        if ($user->isGroupMember()) {    
            $mysql->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '` 
                                SET `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\', 
                                    `teamId` = \'0\' 
                                WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        } else {
            $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_memberof . '` (`userId`, `groupId`, `teamId`) 
                                VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                        \'' . $mysql->real_escape_string($group->getId()) . '\', 
                                        \'0\');');
        }
        
        $mysql->close();
    }
    
    /*
     * Removes a user from a group.
     */
    public static function removeUserFromGroup($user) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_memberof . '` 
                            WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
    }
}
?>