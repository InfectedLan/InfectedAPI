<?php
require_once 'session.php';
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/restrictedpage.php';
require_once 'objects/group.php';
require_once 'objects/team.php';

class RestrictedPageHandler {
    /*
     * Get page by the internal id.
     */
    public static function getPage($id) {
		$mysql = MySQL::open(Settings::db_name_infected_crew);
		
		$result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
								 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
		
		$mysql->close();
            
		return $result->fetch_object('RestrictedPage');
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
                                             WHERE `name` = \'' . $mysql->real_escape_string($name) . '\' 
                                             AND (`groupId` = \'0\' OR `groupId` = \'' . $mysql->real_escape_string($user->getGroup()->getId()) . '\') 
                                             AND `teamId` = \'0\';');
                }
				
				$mysql->close();
				
				return $result->fetch_object('RestrictedPage');
            }
        }
    }
    
    /*
     * Get a list of all pages.
     */
    public static function getPages() {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`;');
        
        $mysql->close();

        $restrictedPageList = array();
        
        while ($object = $result->fetch_object('RestrictedPage')) {
            array_push($restrictedPageList, $object);
        }

        return $restrictedPageList;
    }
    
    /* 
     * Get a list of pages for specified group.
     */
    public static function getPagesForGroup(Group $group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
                                 WHERE `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\'
                                 AND `teamId` = \'0\';');
        
		$mysql->close();
		
        $restrictedPageList = array();
        
        while ($object = $result->fetch_object('RestrictedPage')) {
            array_push($restrictedPageList, $object);
        }

        return $restrictedPageList;
    }
    
	/* 
     * Get a list of all pages for specified group, ignoring the teamId.
     */
    public static function getAllPagesForGroup(Group $group) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
                                 WHERE `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\';');
        
		$mysql->close();
		
        $restrictedPageList = array();
        
        while ($object = $result->fetch_object('RestrictedPage')) {
            array_push($restrictedPageList, $object);
        }

        return $restrictedPageList;
    }
	
    /*
     * Get a list of pages for specified team.
     */
    public static function getPagesForGroupAndTeam(Group $group, Team $team) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_crew_pages . '`
                                 WHERE `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\'
                                 AND (`teamId` = \'' . $mysql->real_escape_string($team->getId()) . '\' OR `teamId` = \'0\');');
        
		$mysql->close();
		
        $restrictedPageList = array();
        
        while ($object = $result->fetch_object('RestrictedPage')) {
            array_push($restrictedPageList, $object);
        }

        return $restrictedPageList;
    }
    
    /* 
     * Create a new page.
     */
    public static function createPage($name, $title, $content, Group $group, Team $team) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_crew_pages . '` (`name`, `title`, `content`, `groupId`, `teamId`) 
                       VALUES (\'' . $mysql->real_escape_string($name) . '\', 
                               \'' . $mysql->real_escape_string($title) . '\', 
                               \'' . $mysql->real_escape_string($content) . '\', 
                               \'' . $mysql->real_escape_string(($group != null ? $group->getId() : '0')) . '\', 
                               \'' . $mysql->real_escape_string(($team != null ? $team->getId() : '0')) . '\')');
        
        $mysql->close();
    }
    
	/*
     * Update a page.
     */
    public static function updatePage(RestrictedPage $page, $title, $content, Group $group, Team $team) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_crew_pages . '` 
                       SET `title` = \'' . $mysql->real_escape_string($title) . '\', 
                           `content` = \'' . $mysql->real_escape_string($content) . '\',
						   `groupId` = \'' . $mysql->real_escape_string($group->getId()) . '\', 
						   `teamId` = \'' . $mysql->real_escape_string($team->getId()) . '\'
                       WHERE `id` = \'' . $mysql->real_escape_string($page->getId()) . '\';');
        
        $mysql->close();
    }
	
    /*
     * Remove a page.
     */
    public static function removePage(RestrictedPage $page) {
        $mysql = MySQL::open(Settings::db_name_infected_crew);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_crew_pages . '` 
                       WHERE `id` = \'' . $mysql->real_escape_string($page->getId()) . '\';');
        
        $mysql->close();
    }
}
?>