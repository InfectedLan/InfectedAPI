<?php
require_once 'session.php';
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/restrictedpage.php';
require_once 'objects/user.php';

class RestrictedPageHandler {
    /*
     * Get page by id.
     */
    public static function getPage($id) {
		$mysql = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
								 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
		
		
		$row = $result->fetch_array();
			
		$mysql->close();
            
		if ($row) {
			return new RestrictedPage($row['id'], 
									  $row['name'], 
									  $row['title'], 
									  $row['content'], 
									  $row['groupId'], 
									  $row['teamId']);
        }
    }
    
    /* 
     * Get page by name.
     */
    public static function getPageByName($name) {
        if (Session::isAuthenticated()) {
			$user = Session::getCurrentUser();
			
			if ($user->hasPermission('*') ||
                $user->isGroupMember()) {
                $mysql = MySQL::open(Settings::db_name_infected_crew);
				
				if ($user->hasPermission('*')) {
					$result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
                                             WHERE `name` = \'' . $mysql->real_escape_string($name) . '\';');
				} else if ($user->isGroupLeader()) {
					$result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
                                             WHERE `name` = \'' . $mysql->real_escape_string($name) . '\' 
                                             AND (`groupId` = \'0\' OR `groupId` = \'' . $mysql->real_escape_string($user->getGroup()->getId()) . '\');');
                } else if ($user->isTeamMember()) {
                    $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
                                             WHERE `name` = \'' . $mysql->real_escape_string($name) . '\' 
                                             AND (`groupId` = \'0\' OR `groupId` = \'' . $mysql->real_escape_string($user->getGroup()->getId()) . '\') 
                                             AND (`teamId` = \'0\' OR `teamId` = \'' . $mysql->real_escape_string($user->getTeam()->getId()) . '\');');
                } else {
                    $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
                                             WHERE `name` = \'' . $mysql->real_escape_string($id) . '\' 
                                             AND (`groupId` = \'0\' OR `groupId` = \'' . $mysql->real_escape_string($user->getGroup()->getId()) . '\') 
                                             AND `teamId` = \'0\';');
                }
				
				$mysql->close();
            }
			
            $row = $result->fetch_array();
            
            if ($row) {
                return self::getPage($row['id']);
            }
        }
    }
    
    /*
     * Get a list of all pages.
     */
    public static function getPages() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '`;');
        
        $pageList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($pageList, self::getPage($row['id']));
        }
        
        $mysql->close();

        return $pageList;
    }
    
    /* 
     * Get a list of pages for specified group.
     */
    public static function getPagesForGroup($group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '`
                                 WHERE `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\'
                                 AND `teamId` = \'0\';');
        
		$mysql->close();
		
        $pageList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($pageList, self::getPage($row['id']));
        }
        
        return $pageList;
    }
    
	/* 
     * Get a list of all pages for specified group, ignoring the teamId.
     */
    public static function getAllPagesForGroup($group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '`
                                 WHERE `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\';');
        
		$mysql->close();
		
        $pageList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($pageList, self::getPage($row['id']));
        }
        
        return $pageList;
    }
	
    /*
     * Get a list of pages for specified team.
     */
    public static function getPagesForGroupAndTeam($group, $team) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_crew_pages . '`
                                 WHERE `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\'
                                 AND (`teamId` = \'' . $mysql->real_escape_string($team->getId()) . '\' OR `teamId` = \'0\');');
        
		$mysql->close();
		
        $pageList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($pageList, self::getPage($row['id']));
        }
        
        return $pageList;
    }
    
    /* 
     * Create a new page.
     */
    public static function createPage($name, $title, $content, $groupId, $teamId) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_pages . '` (`name`, `title`, `content`, `groupId`, `teamId`) 
                       VALUES (\'' . $mysql->real_escape_string($name) . '\', 
                               \'' . $mysql->real_escape_string($title) . '\', 
                               \'' . $mysql->real_escape_string($content) . '\', 
                               \'' . $mysql->real_escape_string($groupId) . '\', 
                               \'' . $mysql->real_escape_string($teamId) . '\')');
        
        $mysql->close();
    }
    
	/*
     * Update a page.
     */
    public static function updatePage($page, $title, $content) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_pages . '` 
                       SET `title` = \'' . $mysql->real_escape_string($title) . '\', 
                           `content` = \'' . $mysql->real_escape_string($content) . '\' 
                       WHERE `id` = \'' . $mysql->real_escape_string($page->getId()) . '\';');
        
        $mysql->close();
    }
	
    /*
     * Remove a page.
     */
    public static function removePage($id) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_pages . '` 
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
}
?>