<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/clan.php';
require_once 'handlers/eventhandler.php';
class ClanHandler {
	public static function getClan($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` WHERE `id` = \'$id\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if($row) {
			return new Clan($row['id'], $row['chief'], $row['name'], $row['event'], $row['tag']);
		}
	}

	public static function createClan($owner, $name)
	{
		$event = EventHandler::getCurrentEvent();

		$con = MySQL::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_clans . '` (`chief`, `name`, `event`) 
					VALUES (\'' . $owner->getId() . '\', \'' . $con->real_escape_string($name) . '\', \'' . $event->getId() . '\');');
		
		MySQL::close($con);
	}
}
?>