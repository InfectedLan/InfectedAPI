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
			return new Match($row['id'], $row['scheduledTime'], $row['connectDetails'], $row['winner'], $row['state'], $row['compoId']);
		}
	}

	public static function getPendingMatches($compo) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` WHERE `compoId` = ' . $con->real_escape_string( $compo->getId() ) . ';');

		$matchList = array();

		while($row = mysqli_fetch_array($result)) {
			$match = self::getMatch($row['id']);
			if($match->getScheduledTime() > time()) {
				array_push($matchList, $match);
			} else if(!self::isReady($match)) {
				array_push($matchList, $match);
			}
		}

		MySQL::close($con);

		return $matchList;
	}

	public static function getCurrentMatches($compo) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		//Picks matches that "should" be running.
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` WHERE `compoId` = ' . $con->real_escape_string( $compo->getId() ) . ' AND `winner` = 0 AND `scheduledTime` < ' . time() . ';');

		$matchList = array();

		while($row = mysqli_fetch_array($result)) {
			$match = self::getMatch($row['id']);
			if(self::isReady($match)) {
				array_push($matchList, $match);
			}
		}

		MySQL::close($con);

		return $matchList;
	}

	public static function getFinishedMatches($compo) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		//Picks matches that "should" be running.
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_matches . '` WHERE `compoId` = ' . $con->real_escape_string( $compo->getId() ) . ' AND `winner` != 0;');

		$matchList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($matchList, self::getMatch($row['id']));
		}

		MySQL::close($con);

		return $matchList;
	}

	public static function getMatchForClan($clan) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` WHERE `type` = ' . Settings::compo_match_participant_type_clan . ' AND `participantId` = ' . $con->real_escape_string($clan->getId()) . ';');

		MySQL::close($con);
		while($row = mysqli_fetch_array($result)) {
		 	$match = self::getMatch($row['matchId']);

		 	if($match->getWinner() == 0 && $match->getScheduledTime() < time()) {
		 		return $match;
		 	}
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

	public static function setWinner($match, $clan) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		//Set winner of match
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_compo_matches . '` SET `winner` = ' . $con->real_escape_string($clan->getId()) . ' WHERE `id` = ' . $con->real_escape_string($match->getId()) . ';');

		//Update match results
		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '` SET `type` = 0, `participantId` = ' . $con->real_escape_string($clan->getId()) . ' WHERE `type` = 1 AND `participantId` = ' . $con->real_escape_string($match->getId()) . ';');

		$looser = null;

		$participants = self::getParticipants($match);

		foreach($participants as $participant) {
			if($participant->getId() != $clan->getId()) {
				$looser = $participant;
			}
		}

		mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '` SET `type` = 0, `participantId` = ' . $con->real_escape_string($looser->getId()) . ' WHERE `type` = 2 AND `participantId` = ' . $con->real_escape_string($match->getId()) . ';');
	
		MySQL::open(Settings::db_name_infected_compo);
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

	public static function getParticipantString($match) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` WHERE `matchId` = ' . $con->real_escape_string($match->getId()) . ';');

		$stringArray = array();

		while($row = mysqli_fetch_array($result)) {
			if($result['type'] == Settings::compo_match_participant_type_match_winner) {
				array_push($stringArray, "Winner of match " . $result['participantId']);
			} else if($result['type'] == Settings::compo_match_participant_type_match_looser) {
				array_push($stringArray, "Looser of match " . $result['participantId']);
			} else if($result['type'] == Settings::compo_match_participant_type_clan) {
				$clan = ClanHandler::getClan($result['participantId']);
				array_push($stringArray, $clan->getName() . " (id " . $clan->getId() . ")");
			} 
		}

		MySQL::close($con);

		return $stringArray;
	}

	//Checks if the match can run(If we have enough participants. Returns false if we have to wait for earlier matches to complete)
	public static function isReady($match) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` WHERE `matchId` = ' . $con->real_escape_string($match->getId()) . ';');

		$hasParticipants = false;

		MySQL::close($con);

		while($row = mysqli_fetch_array($result)) {
			$hasParticipants = true;
			if($row['type'] == Settings::compo_match_participant_type_match_winner || $row['type'] == Settings::compo_match_participant_type_match_looser) {
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

	public static function acceptMatch($user, $match) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_compo_readyusers . '` (`userId`, `matchId`) VALUES (\'' . $con->real_escape_string($user->getId()) . '\', \'' . $con->real_escape_string($match->getId()) . '\');');

		MySQL::close($con);
	}

	public static function allHasAccepted($match) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '` WHERE `type` = 0 AND `matchId` = ' . $con->real_escape_string($match->getId()) . ';');

		//Iterate through clans
		while($row = mysqli_fetch_array($result)) {
			$users = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '` WHERE `clanId` = ' . $row['participantId'] . ';');
			while($userRow = mysqli_fetch_array($users)) {
				$userCheck = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_readyusers . '` WHERE `userId` = ' . $userRow['userId'] . ' AND `matchId` = ' . $con->real_escape_string($match->getId()) . ';');
				
				$row = mysqli_fetch_array($userCheck);
				if($row) {

				} else {
					return false;
				}

			}
		}

		MySQL::close($con);

		return true;
	}
}
?>