<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/clan.php';
class ClanHandler {
	public static function getClan($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_clans . '` WHERE `id` = \'$id\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if($row) {
			return new Clan($row['id'], $row['chief'], $row['name'], $row['event']);
		}
	}
}
?>