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
	public static function getNumBanned($consumerId) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_votes . '` WHERE `consumerId` = ' . $con->real_escape_string($consumerId) . ';');

		$count = 0;

		while($row = mysqli_fetch_array($result)) {
			$count++;
		}

		MySQL::close($con);

		return $count;
	}
	public static function getCurrentBanner($numBanned) {
		$turnArray = array(0, 1, 0, 1, 1, 0, 2);
		return $turnArray[$numBanned];
	}
	public static function banMap($voteOption, $consumerId) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_votes . '` (`consumerId`, `voteOptionId`) VALUES (\'' . $con->real_escape_string($consumerId) . '\', \'' . $con->real_escape_string($voteOption->getId()) . '\');');
	
		MySQL::close($con);
	}
}
?>