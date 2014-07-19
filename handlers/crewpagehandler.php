<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'utils.php';
require_once 'objects/crewpage.php';
require_once 'objects/user.php';

class CrewPageHandler {
	/* Get page by id */
	public static function getPage($id) {
		if (Utils::isAuthenticated()) {
			$user = Utils::getUser();
			
			if ($user->isGroupMember()) {
				$con = MySQL::open(Settings::db_name_infected_crew);
				
				if ($user->isTeamMember()) {
					$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_infected_crew_pages . ' WHERE id=\'' . $id . '\' AND (groupId=\'0\' OR groupId=\'' . $user->getGroup()->getId() . '\') AND (teamId=\'0\' OR teamId=\'' . $user->getTeam()->getId() . '\')');
				} else {
					$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_infected_crew_pages . ' WHERE id=\'' . $id . '\' AND (groupId=\'0\' OR groupId=\'' . $user->getGroup()->getId() . '\') AND teamId=\'0\'');
				}
				
				$row = mysqli_fetch_array($result);
				
				MySQL::close($con);

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
			$con = MySQL::open(Settings::db_name_infected_crew);
			
			$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_infected_crew_pages . ' WHERE name=\'' . $name . '\'');
			
			$row = mysqli_fetch_array($result);
			
			MySQL::close($con);

			if ($row) {
				return self::getPage($row['id']);
			}
		}
	}
	
	/* Get a list of all pages */
	public static function getPages() {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_infected_crew_pages);
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, self::getPage($row['id']));
		}
		
		MySQL::close($con);

		return $pageList;
	}
	
	/* Get a list of pages for specified group */
	public static function getPagesForGroup($groupId) {
		return self::getPagesForTeam($groupId, 0);
	}
	
	/* Get a list of pages for specified team */
	public static function getPagesForTeam($groupId, $teamId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_infected_crew_pages . ' WHERE groupId=\'' . $groupId . '\' AND teamId=\'' . $teamId . '\'');
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, self::getPage($row['id']));
		}

		MySQL::close($con);
		
		return $pageList;
	}
	
	/* Create a new page */
	public static function createPage($name, $title, $content, $groupId, $teamId) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'INSERT INTO ' . Settings::db_table_infected_crew_pages . ' (name, title, content, groupId, teamId) 
							VALUES (\'' . $name . '\', 
									\'' . $title . '\', 
									\'' . $content . '\', 
									\'' . $groupId . '\', 
									\'' . $teamId . '\')');
		
		MySQL::close($con);
	}
	
	/* Remove a page */
	public static function removePage($id) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'DELETE FROM ' . Settings::db_table_infected_crew_pages . ' WHERE id=\'' . $id . '\'');
		
		MySQL::close($con);
	}
	
	/* Update a page */
	public static function updatePage($id, $title, $content) {
		$con = MySQL::open(Settings::db_name_infected_crew);
		
		mysqli_query($con, 'UPDATE ' . Settings::db_table_infected_crew_pages . ' SET title=\'' . $title . '\', content=\'' . $content . '\' WHERE id=\'' . $id . '\'');
		
		MySQL::close($con);
	}
}
?>