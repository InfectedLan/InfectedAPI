<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/readyhandler.php';

class ReadyHandlerHandler {
	public static function getReadyHandler($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_readyInstances . '` 
									  WHERE `id` = \'' . $id . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new ReadyHandler($row['id'], 
									$row['compoId']);
		}
	}
}
?>