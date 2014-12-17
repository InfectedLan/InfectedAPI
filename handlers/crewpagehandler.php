<?php
require_once 'session.php';
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/crewpage.php';
require_once 'objects/user.php';

class CrewPageHandler {
    /* Get page by id */
    public static function getPage($id) {
        if (Session::isAuthenticated()) {
            $user = Session::getCurrentUser();
            
            if ($user->isGroupMember()) {
                $mysql = MySQL::open(Settings::db_name_infected_crew);
                
                if ($user->isTeamMember()) {
                    $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
                                                  WHERE `id` = \'' . $mysql->real_escape_string($id) . '\' 
                                                  AND (`groupId` = \'0\' OR `groupId` = \'' . $mysql->real_escape_string($user->getGroup()->getId()) . '\') 
                                                  AND (`teamId` = \'0\' OR `teamId` = \'' . $mysql->real_escape_string($user->getTeam()->getId()) . '\');');
                } else {
                    $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '` 
                                                  WHERE `id` = \'' . $mysql->real_escape_string($id) . '\' 
                                                  AND (`groupId` = \'0\' OR `groupId` = \'' . $mysql->real_escape_string($user->getGroup()->getId()) . '\') 
                                                  AND `teamId` = \'0\';');
                }
                
                $row = mysqli_fetch_array($result);
                
                $mysql->close();

                if ($row) {
                    return new CrewPage($row['id'], 
                                    $row['name'], 
                                    $row['title'], 
                                    $row['content'], 
                                    $row['groupId'], 
                                    $row['teamId']);
                }
            }
        }
    }
    
    /* Get page by name */
    public static function getPageByName($name) {
        if (Utils::isAuthenticated()) {
            $user = Utils::getUser();
            $mysql = MySQL::open(Settings::db_name_infected_crew);
            
            $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '` 
                                          WHERE `name` = \'' . $mysql->real_escape_string($name) . '\';');
            
            $row = mysqli_fetch_array($result);
            
            $mysql->close();

            if ($row) {
                return self::getPage($row['id']);
            }
        }
    }
    
    /* Get a list of all pages */
    public static function getPages() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '`;');
        $pageList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($pageList, self::getPage($row['id']));
        }
        
        $mysql->close();

        return $pageList;
    }
    
    /* Get a list of pages for specified group */
    public static function getPagesForGroup($groupId) {
        return self::getPagesForTeam($groupId, 0);
    }
    
    /* Get a list of pages for specified team */
    public static function getPagesForTeam($groupId, $teamId) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '` 
                                      WHERE `groupId` = \'' . $mysql->real_escape_string($groupId) . '\' 
                                      AND `teamId` = \'' . $mysql->real_escape_string($teamId) . '\';');
        $pageList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($pageList, self::getPage($row['id']));
        }

        $mysql->close();
        
        return $pageList;
    }
    
    /* Create a new page */
    public static function createPage($name, $title, $mysqltent, $groupId, $teamId) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_pages . '` (`name`, `title`, `content`, `groupId`, `teamId`) 
                            VALUES (\'' . $mysql->real_escape_string($name) . '\', 
                                    \'' . $mysql->real_escape_string($title) . '\', 
                                    \'' . $mysql->real_escape_string($mysqltent) . '\', 
                                    \'' . $mysql->real_escape_string($groupId) . '\', 
                                    \'' . $mysql->real_escape_string($teamId) . '\')');
        
        $mysql->close();
    }
    
    /* Remove a page */
    public static function removePage($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('DELETE FROM ' . Settings::db_table_infected_crew_pages . ' 
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\'');
        
        $mysql->close();
    }
    
    /* Update a page */
    public static function updatePage($id, $title, $mysqltent) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_pages . '` 
                            SET `title` = \'' . $mysql->real_escape_string($title) . '\', 
                                `content` = \'' . $mysql->real_escape_string($mysqltent) . '\' 
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
}
?>