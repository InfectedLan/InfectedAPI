<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/vote.php';
class VoteHandler {
	public static function getVote($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_votes . '` WHERE `id` = \'$id\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if($row) {
			return new Vote($row['id'], $row['consumerId'], $row['voteOptionId']);
		}
	}

	public static function isVoted($voteOption) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_votes . '` WHERE `voteOptionId` = '. $con->real_escape_string($voteOption->getId()) . ';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return true;
		}
		return false;
	}
}
?>