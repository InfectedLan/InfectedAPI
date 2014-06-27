<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'sitePage.php';

class SitePageHandler {
	// Get page.
	public static function getSitePage($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_site_pages . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new SitePage($row['id'], $row['name'], $row['title'], $row['content']);
		}
	}
	
	// Get page.
	public static function getSitePageByName($name) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_site_pages . ' WHERE name=\'' . $name . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return self::getPage($row['id']);
		}
	}
	
	// Get a list of all pages
	public static function getSitePages() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_site_pages);
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, self::getPage($row['id']));
		}
		
		MySQL::close($con);

		return $pageList;
	}
}
?>