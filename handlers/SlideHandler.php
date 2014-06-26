<?php
require_once 'mysql.php';
require_once 'settings.php';

	class SlideHandler {
		public static function getSlide($id) {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_slides . ' WHERE id=\'' . $id . '\'');
			$row = mysqli_fetch_array($result);
			
			if ($row) {
				return new Slide($row['id'], $row['start'], $row['end'], $row['title'], $row['content'], $row['published']);
			}
			
			MySQL::close($con);
		}
		
		public static function getSlides() {
			$con = MySQL::open(Settings::db_name_infected);
			
			$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_slides . ' ORDER BY start');
			$slideList = array();
			
			while ($row = mysqli_fetch_array($result)) {
				$slide = self::getSlide($row['id']);
				$now = date('U');
				
				if ($slide->getStart() >= $now - $first * 60 * 60 ||
					$slide->getEnd() >= $now + $last * 60 * 60) {
					array_push($slideList, $slide);
				}
			}
			
			return $slideList;
			
			MySQL::close($con);
		}
	}
?>