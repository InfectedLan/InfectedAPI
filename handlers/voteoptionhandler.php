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
		
		if ($row) {
			return new VoteOption($row['id'], $row['compoId'], $row['thumbnailUrl'], $row['name']);
		}
	}

	public static function getVoteOptionsForCompo($compo) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` 
									  WHERE `compoId` = '. $con->real_escape_string($compo->getId()) . ';');
		
		MySQL::close($con);

		$returnArray = array();

		while ($row = mysqli_fetch_array($result)) {
			array_push($returnArray, self::getVoteOption($row['id']));
		}
		
		return $returnArray;
	}

	public static function isVoted($voteOption, $match) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_votes . '` 
								      WHERE `voteOptionId` = '. $con->real_escape_string($voteOption->getId()) . ' AND `consumerId` = ' . $con->real_escape_string($match->getId()) . ';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		return $row ? true : false;
	}
}
?>