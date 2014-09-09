<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/compo.php';
class CompoHandler {
	public static function getCompo($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_compos . '` WHERE `id` = \'$id\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if($row) {
			return new Compo($row['id'], $row['startTime'], $row['registrationDeadline'], $row['name'], $row['desc']);
		}
	}
}
?>