<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/page.php';

class PageHandler {
	// Get page.
	public static function getPage($id) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_main_pages . '` 
									  WHERE id=\'' . $con->real_escape_string($id) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new Page($row['id'], 
							$row['name'], 
							$row['title'], 
							$row['content']);
		}
	}
	
	// Get page.
	public static function getPageByName($name) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_main_pages . '`
									  WHERE `name` = \'' . $con->real_escape_string($name) . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return self::getPage($row['id']);
		}
	}
	
	// Get a list of all pages
	public static function getPages() {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_main_pages . '`;');
									  
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, self::getPage($row['id']));
		}
		
		MySQL::close($con);

		return $pageList;
	}
	
	/* 
	 * Create a new page.
	 */
	public static function createPage($name, $title, $content) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_main_pages . '` (`name`, `title`, `content`) 
							VALUES (\'' . $con->real_escape_string($name) . '\', 
									\'' . $con->real_escape_string($title) . '\', 
									\'' . $con->real_escape_string($content) . '\')');
		
		MySQL::close($con);
	}
	
	/*
	 * Remove a page.
	 */
	public static function removePage($id) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_main_pages . '` 
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
	
	/*
	 * Update a page.
	 */
	public static function updatePage($id, $name, $title, $content) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_main_pages . '` 
							SET `name` = \'' . $con->real_escape_string($name) . '\', 
								`title` = \'' . $con->real_escape_string($title) . '\', 
								`content` = \'' . $con->real_escape_string($content) . '\' 
							WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		MySQL::close($con);
	}
}
?>