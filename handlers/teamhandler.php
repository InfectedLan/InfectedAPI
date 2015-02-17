<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/team.php';
require_once 'objects/user.php';
require_once 'objects/group.php';

class TeamHandler {
    /* Get a team by id */
    public static function getTeam($id) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_teams . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
		
		return $result->fetch_object('Team');
    }
    
    /* Get a group by userId */
    public static function getTeamForUser(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `teamId` FROM `' . Settings::db_table_infected_crew_memberof . '` 
                                    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								    AND `userId` = \'' . $user->getId() . '\';');
         
        $database->close();

        $row = $result->fetch_array();
        
        if ($row) {
            return self::getTeam($row['teamId']);
        }
    }
    
    /* Get an array of all teams */
    public static function getTeams() {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_teams . '`;');
        
        $database->close();

        $teamList = array();

        while ($object = $result->fetch_object('Team')) {
            array_push($teamList, $object);
        }
        
        return $teamList;
    }
    
    /* Get an array of all teams */
    public static function getTeamsForGroup(Group $group) {
        $database = Database::open(Settings::db_name_infected_crew);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_crew_teams . '`
                                    WHERE `groupId` = \'' . $group->getId() . '\';');
        
        $database->close();

        $teamList = array();
        
        while ($object = $result->fetch_object('Team')) {
            array_push($teamList, $object);
        }
        
        return $teamList;
    }
    
    /* Create a new team */
    public static function createTeam(Event $event, Group $group, $name, $title, $description, $leader) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_crew_teams . '` (`eventId`, `groupId`, `name`, `title`, `description`, `leaderId`) 
                          VALUES (\'' . $event->getId() . '\', 
                                  \'' . $group->getId() . '\', 
                                  \'' . $database->real_escape_string($name) . '\', 
                                  \'' . $database->real_escape_string($title) . '\', 
                                  \'' . $database->real_escape_string($description) . '\', 
                                  \'' . $leader->getId() . '\')');
        
        $database->close();
    }

    /* 
     * Update a team.
     */
    public static function updateTeam(Team $team, Group $group, $name, $title, $description, $leader) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('UPDATE `' . Settings::db_table_infected_crew_teams . '` 
                          SET `groupId` = \'' . $group->getId() . '\', 
                              `name` = \'' . $database->real_escape_string($name) . '\', 
                              `title` = \'' . $database->real_escape_string($title) . '\', 
                              `description` = \'' . $database->real_escape_string($description) . '\', 
                              `leaderId` = \'' . $leader->getId() . '\' 
                          WHERE `id` = \'' . $team->getId() . '\';');
        
        $database->close();
    }
    
    /*
     * Remove a team.
     */
    public static function removeTeam(Group $group, Team $team) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_crew_teams . '` 
                          WHERE `id` = \'' . $team->getId() . '\'
                          AND `groupId` = \'' . $group->getId() . '\';');
        
        $database->close();
    }
    
    /*
     * Returns an array of users that are members of this team.
     */
    public static function getMembers(Group $group, Team $team) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `' . Settings::db_table_infected_users . '`.* FROM `' . Settings::db_table_infected_users . '`
                                    LEFT JOIN `' . Settings::db_name_infected_crew . '`.`' . Settings::db_table_infected_crew_memberof . '`
                                    ON `' . Settings::db_table_infected_users . '`.`id` = `userId` 
                                    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								    AND `groupId` = \'' . $group->getId() . '\'
                                    AND `teamId` = \'' . $team->getId() . '\' 
                                    ORDER BY `firstname` ASC;');
        
        $database->close();

        $memberList = array();

        while ($object = $result->fetch_object('User')) {
            array_push($memberList, $object);
        }
        
        return $memberList;
    }
    
    /*
     * Is member of a team which means it's not a plain user.
     */
    public static function isTeamMember(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_memberof. '` 
                                    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								    AND `userId` = \'' . $user->getId() . '\' 
                                    AND `teamId` != \'0\';');
        
        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Return true if user is leader for a team.
     */
    public static function isTeamLeader(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_teams . '` 
                                    WHERE `leaderId` = \'' . $user->getId() . '\';');
            
        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Sets the users team.
     */
    public static function changeTeamForUser(User $user, Group $group, Team $team) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        if ($user->isGroupMember()) {    
            $database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '` 
                              SET `teamId` = \'' . $team->getId() . '\' 
						      WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                              AND `userId` = \'' . $user->getId() . '\' 
                              AND `groupId` = \'' . $group->getId() . '\';');    
        }
        
        $database->close();
    }
    
    /*
     * Removes a user from a team.
     */
    public static function removeUserFromTeam(User $user) {
        $database = Database::open(Settings::db_name_infected_crew);
        
        $database->query('UPDATE `' . Settings::db_table_infected_crew_memberof . '` 
                          SET `teamId` = \'0\'
                          WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
					      AND `userId` = \'' . $user->getId() . '\';');    
        
        $database->close();
    }
}
?>