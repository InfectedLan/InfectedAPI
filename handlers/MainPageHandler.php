<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/Settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/MySQL.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/objects/MainPage.php';

class MainPageHandler {
	// Get page.
	public static function getPage($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_main_pages . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return new MainPage($row['id'], $row['name'], $row['title'], $row['content']);
		}
	}
	
	// Get page.
	public static function getPageByName($name) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_main_pages . ' WHERE name=\'' . $name . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);

		if ($row) {
			return self::getPage($row['id']);
		}
	}
	
	// Get a list of all pages
	public static function getPages() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_main_pages);
		$pageList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($pageList, self::getPage($row['id']));
		}
		
		MySQL::close($con);

		return $pageList;
	}
}
?>