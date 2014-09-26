<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/voteoption.php';
class VoteOptionHandler {
	public static function getVoteOption($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` WHERE `id` = \'' . $id . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if($row) {
			return new VoteOption($row['id'], $row['compoId'], $row['thumbnailUrl'], $row['name']);
		}
	}

	public static function getVoteOptionsForCompo($compo) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` WHERE `id` = \'' . $id . '\' AND `compoId` = '. $con->real_escape_string($compo->getId()) . ';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if($row) {
			return self::getVoteOption($row['id']);
		}
	}
}
?>