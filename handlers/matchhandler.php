<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/match.php';
require_once 'handlers/clanhandler.php';

/*
 * EPIC WARNING:
 * 
 * participantOfMatch should only have a clan linked if the clan is ready to play that match
 */

class MatchHandler {
	public static function getMatch($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` WHERE `id` = \'' . $id . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if($row) {
			return new Match($row['id'], $row['scheduledTime'], $row['connectDetails'], $row['winner'], $row['state']);
		}
	}

	public static function getMatchForClan($clan) {
		 $con = MySQL::open(Settings::db_name_infected_compo);

		 $result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` WHERE `type` = ' . Settings::compo_match_participant_type_clan . ' 
		 						AND `participantId` = ' . $con->real_escape_string($clan->getId()) . ';');

		 $row = mysqli_fetch_array($result);

		 MySQL::close($con);

		 if($row) {
		 	return self::getMatch($row['matchId']);
		 }
	}

	public static function hasClanMatch($clan) {
		return null !== self::getMatchForClan($clan);
	}

	//Unstable if user has multiple matches happening
	public static function getMatchForUser($user) {
		$clans = ClanHandler::getClansForUser($user);
		foreach($clans as $clan) {
			$match = self::getMatchForClan($clan);
			if(isset($match)) {
				return $match;
			}
		}
	}

	public static function getParticipants($match) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` WHERE `matchId` = ' . $con->real_escape_string($match->getId()) . ' AND `type` = ' . Settings::compo_match_participant_type_clan . ';');
	
		$clanArray = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($clanArray, ClanHandler::getClan($row['participantId']) );
		}

		MySQL::close($con);

		return $clanArray;
	}

	public static function isReady($match) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` WHERE `matchId` = ' . $con->real_escape_string($match->getId()) . ';');

		$hasParticipants = false;

		MySQL::close($con);

		while($row = mysqli_fetch_array($result)) {
			$hasParticipants = true;
			if($row['type'] == Settings::compo_match_participant_type_match_result) {
				return false;
			}
		}

		return $hasParticipants;
	}

	//Used in the ready check
	public static function isUserReady($user, $match) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_readyusers . '` WHERE `userId` = ' . $con->real_escape_string($user->getId()) . ' AND `matchId` = ' . $con->real_escape_string($match->getId()) . ';');
	
		$row = mysqli_fetch_array($result);
		if($row) {
			return true;
		}
		return false;
	}
}
?>