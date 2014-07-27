<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/slide.php';

class SlideHandler {
	public static function getSlide($id) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_main_slides . '` 
									  WHERE `id` = \'' . $id . '\';');
										
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new Slide($row['id'], 
							 $row['start'], 
							 $row['end'], 
							 $row['title'], 
							 $row['content'], 
							 $row['published']);
		}
	}
	
	public static function getSlides() {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id` 
									  FROM `' . Settings::db_table_infected_main_slides . '` 
									  ORDER BY `start`;');
		$slideList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($slideList, self::getSlide($row['id']));
		}
		
		MySQL::close($con);
		
		return $slideList;
	}
}
?>