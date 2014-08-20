<?php
require_once 'settings.php';
require_once 'mysql.php';

class CityDictionary {
	/* 
	 * Returns the city from given postalcode.
	 */
	public static function getCity($postalcode) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `city` FROM `' . Settings::db_table_infected_postalcodes . '`
									  WHERE `code` = \'' . $con->real_escape_string($postalcode . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return ucfirst(strtolower($row['city']));
		}
	}
	
	/*
	 * Returns the postalcode for given city.
	 */
	public static function getPostalCode($city) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT `code` FROM `' . Settings::db_table_infected_postalcodes . '` 
									  WHERE `city` = \'' . $con->real_escape_string($city) . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return $row['city'];
		}
	}
}
?>
