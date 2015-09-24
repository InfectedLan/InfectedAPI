<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/clanhandler.php';
require_once 'objects/match.php';
require_once 'objects/compo.php';
require_once 'objects/chat.php';
require_once 'objects/clan.php';
require_once 'objects/user.php';
require_once 'objects/event.php';

/*
 * EPIC WARNING:
 *
 * participantOfMatch should only have a clan linked if the clan is ready to play that match
 */
class MatchHandler {
	//State of the 'participantOf' table responsible for the participants of a match
	const participantof_state_clan = 0;
	const participantof_state_winner = 1;
	const participantof_state_looser = 2;

	/*
	 * Get a match by the internal id.
	 */
	public static function getMatch($id) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '`
								 								WHERE `id` = \'' . $id . '\';');

		$database->close();

		return $result->fetch_object('Match');
	}

	/*
	 * Returns a list of all matches.
	 */
	public static function getMatches() {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '`;');

		$database->close();

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			$matchList[] = $object;
		}

		return $matchList;
	}

	// Unstable if user has multiple matches happening
	public static function getMatchByUser(User $user) {
		$clanList = ClanHandler::getClansByUser($user);

		foreach ($clanList as $clan) {
			$match = self::getMatchByClan($clan);

			if ($match != null) {
				return $match;
			}
		}
	}

	public static function getMatchByClan(Clan $clan) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '`
																WHERE `id` = (SELECT `matchId` FROM `' . Settings::db_table_infected_compo_participantOfMatch . '`
																						  WHERE `type` = \'' . Settings::compo_match_participant_type_clan . '\'
																						  AND `participantId` = \'' . $clan->getId() . '\');');

		$database->close();

		// TODO: Do this stuff in SQL query instead?
		while ($object = $result->fetch_object('Match')) {
			if ($object->getWinner() == 0 &&
				$object->getScheduledTime() < time()) {
				return $object;
			}
		}
	}

	public static function getPendingMatchesByCompo(Compo $compo) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '`
																WHERE `compoId` = ' . $compo->getId() . ';');

		$database->close();

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			if ($object->getScheduledTime() > time() ||
				!self::isReady($object)) {
				$matchList[] = $object;
			}
		}

		return $matchList;
	}

	public static function getCurrentMatchesByCompo(Compo $compo) {
		$database = Database::open(Settings::db_name_infected_compo);

		//Picks matches that 'should' be running.
		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '`
																WHERE `compoId` = \'' . $compo->getId() . '\'
																AND `winnerId` = \'0\'
																AND `scheduledTime` < \'' . time() . '\';');

		$database->close();

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			if (self::isReady($object)) {
				$matchList[] = $object;
			}
		}

		return $matchList;
	}

	public static function getFinishedMatchesByCompo(Compo $compo) {
		$database = Database::open(Settings::db_name_infected_compo);

		//Picks matches that 'should' be running.
		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '`
																WHERE `compoId` = ' . $compo->getId() . '
																AND `winnerId` != 0;');

		$database->close();

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			$matchList[] = $object;
		}

		return $matchList;
	}

	public static function getMatchesByCompo(Compo $compo) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '`
																WHERE `compoId` = \'' . $compo->getId() . '\';');

		$database->close();

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			$matchList[] = $object;
		}

		return $matchList;
	}

	public static function hasMatchByClan(Clan $clan) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_matches . '`
																WHERE `id` = (SELECT `matchId` FROM `' . Settings::db_table_infected_compo_participantOfMatch . '`
																						  WHERE `type` = \'' . Settings::compo_match_participant_type_clan . '\'
																						  AND `participantId` = \'' . $clan->getId() . '\');');

		$database->close();

		return $result->num_rows > 0;
	}

	public static function addMatchParticipant($type, $participantId, Match $match) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_participantOfMatch . '` (`type`, `participantId`, `matchId`)
										  VALUES (\'' . $database->real_escape_string($type) . '\',
														  \'' . $database->real_escape_string($participantId) . '\',
														  \'' . $match->getId() . '\');');

		if ($type != self::participantof_state_clan &&
			$type != self::participantof_state_looser) {
			$database->query('INSERT INTO `' . Settings::db_table_infected_compo_matchrelationships . '` (`fromCompo`, `toCompo`)
											  VALUES (\'' . $database->real_escape_string($participantId) . '\',
													  		\'' . $match->getId() . '\');');
		}

		$database->close();
	}

	public static function setWinner(Match $match, Clan $clan) {
		$database = Database::open(Settings::db_name_infected_compo);

		// Set winner of match
		$database->query('UPDATE `' . Settings::db_table_infected_compo_matches . '`
										  SET `winnerId` = \'' . $clan->getId() . '\'
										  WHERE `id` = \'' . $match->getId() . '\';');

		// Update match results
		//First, get list of matches we want to change
		$toWinList = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '`
																   WHERE `type` = \'1\'
																   AND `participantId` = \'' . $match->getId() . '\';');

		while ($row = $toWinList->fetch_array()) {
			$database->query('UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '`
											  SET `type` = \'0\',
												  	`participantId` = \'' . $clan->getId() . '\'
											  WHERE `id` = \'' . $row['id'] . '\';');

			$checkingMatchId = MatchHandler::getMatch($row['matchId']);
			ChatHandler::addChatMembers(ChatHandler::getChat($checkingMatchId->getChat()), $clan->getMembers());
		}
		/*
		$database->query('UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '`
							SET `type` = 0, `participantId` = ' . $database->real_escape_string($clan->getId()) . '
							WHERE `type` = 1
							AND `participantId` = ' . $database->real_escape_string($match->getId()) . ';');
		*/
		$looser = null;

		$participantList = self::getParticipants($match);

		foreach ($participantList as $participant) {
			if ($participant->getId() != $clan->getId()) {
				$looser = $participant;
			}
		}

		$toLooseList = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '`
																		 WHERE `type` = \'2\'
																		 AND `participantId` = \'' . $match->getId() . '\';');

		while ($row = $toLooseList->fetch_array()) {
			$database->query('UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '`
											  SET `type` = \'0\',
												  	`participantId` = \'' . $clan->getId() . '\'
											  WHERE `id` = \'' . $row['id'] . '\';');

			$checkingMatchId = MatchHandler::getMatch($row['matchId']);
			ChatHandler::addChatMembers(ChatHandler::getChat($checkingMatchId->getChat()), $clan->getMembers());
		}

		/*$database->query('UPDATE `' . Settings::db_table_infected_compo_participantOfMatch . '`
							SET `type` = 0, `participantId` = ' . $database->real_escape_string($looser->getId()) . '
							WHERE `type` = 2 AND `participantId` = ' . $database->real_escape_string($match->getId()) . ';');*/

		$database->close();
	}

	public static function getParticipantsByMatch(Match $match) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '`
																WHERE `id` IN (SELECT `participantId` FROM `' . Settings::db_table_infected_compo_participantOfMatch . '`
																						   WHERE `matchId` = \'' . $match->getId() . '\'
																						   AND `type` = \'' . Settings::compo_match_participant_type_clan . '\');');

		$database->close();

		$clanList = [];

		while ($object = $result->fetch_object('Clan')) {
			$clanList[] = $object;
		}

		return $clanList;
	}

	public static function getParticipantStringByMatch(Match $match) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '`
																WHERE `matchId` = \'' . $match->getId() . '\';');

		$database->close();

		$stringArray = [];

		while ($row = $result->fetch_array()) {
			if ($row['type'] == Settings::compo_match_participant_type_match_winner) {
				$stringArray[] = 'Winner of match ' . $row['participantId'];
			} else if ($row['type'] == Settings::compo_match_participant_type_match_looser) {
				$stringArray[] = 'Looser of match ' . $row['participantId'];
			} else if ($row['type'] == Settings::compo_match_participant_type_clan) {
				$clan = ClanHandler::getClan($row['participantId']);
				$stringArray[] = $clan->getName() . ' - ' . $clan->getTag() . ' (id ' . $clan->getId() . ')';
			}
		}

		return $stringArray;
	}

	public static function getParticipantTagsByMatch(Match $match) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '`
																WHERE `matchId` = \'' . $match->getId() . '\';');

		$database->close();

		$stringArray = [];

		while ($row = $result->fetch_array()) {
			if ($row['type'] == Settings::compo_match_participant_type_match_winner ||
				$row['type'] == Settings::compo_match_participant_type_match_looser) {
				$stringArray[] = 'TBA';
			} else if ($row['type'] == Settings::compo_match_participant_type_clan) {
				$clan = ClanHandler::getClan($row['participantId']);
				$stringArray[] = $clan->getTag();
			}
		}

		return $stringArray;
	}

	public static function getParticipantsJsonByMatch(Match $match) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '`
																WHERE `matchId` = \'' . $match->getId() . '\';');

		$database->close();

		$jsonArray = [];

		while ($row = $result->fetch_array()) {
			if ($row['type'] == Settings::compo_match_participant_type_clan) {
				$clan = ClanHandler::getClan($row['participantId']);
				$jsonArray[] = ['type' => $row['type'], 'value' => $clan->getName() . ' - ' . $clan->getTag() . ''];
			} else {
				$jsonArray[] = ['type' => $row['type'], 'value' => $row['participantId']];
			}
		}

		return $jsonArray;
	}

	public static function getMatchParents(Match $match) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_matches . '`
																WHERE `id` = (SELECT `fromCompo` FROM `' . Settings::db_table_infected_compo_matchrelationships . '`
																			  			WHERE `toCompo` = \'' . $match->getId() . '\');');

		$database->close();

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			$matchList[] = $object;
		}

		return $matchList;
	}

	// Checks if the match can run(If we have enough participants. Returns false if we have to wait for earlier matches to complete)
	public static function isReady(Match $match) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `type` FROM `' . Settings::db_table_infected_compo_participantOfMatch . '`
																WHERE `matchId` = \'' . $match->getId() . '\';');

		$database->close();

		$hasParticipants = false;

		while ($row = $result->fetch_array()) {
			$hasParticipants = true;

			if ($row['type'] == Settings::compo_match_participant_type_match_winner ||
				$row['type'] == Settings::compo_match_participant_type_match_looser) {
				return false;
			}
		}

		return $hasParticipants;
	}

	// Used in the ready check
	public static function isUserReady(User $user, Match $match) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_readyusers . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `matchId` = \'' . $match->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	public static function acceptMatch(User $user, Match $match) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('INSERT INTO `' . Settings::db_table_infected_compo_readyusers . '` (`userId`, `matchId`)
																VALUES (\'' . $user->getId() . '\',
																				\'' . $match->getId() . '\');');

		$database->close();
	}

	public static function allHasAccepted(Match $match) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantOfMatch . '`
																WHERE `type` = \'0\'
																AND `matchId` = \'' . $match->getId() . '\';');

		// Iterate through clans
		while ($row = $result->fetch_array()) {
			$users = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_memberof . '`
															   WHERE `clanId` = \'' . $row['participantId'] . '\';');

			while ($userRow = $users->fetch_array()) {
				$userCheck = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_readyusers . '`
																		   WHERE `userId` = \'' . $userRow['userId'] . '\'
																		   AND `matchId` = \'' . $match->getId() . '\';');

			 	$row = $userCheck->fetch_array();

				if (!$row) {
					return false;
				}
			}
		}

		$database->close();

		return true;
	}

	public static function createMatch($scheduledTime, $connectData, Compo $compo, $bracketOffset, Chat $chat, $bracket) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_matches . '` (`scheduledTime`, `connectDetails`, `state`, `winnerId`, `compoId`, `bracketOffset`, `chatId`, `bracket`)
											VALUES (\'' . $database->real_escape_string($scheduledTime) . '\',
															\'' . $database->real_escape_string($connectData) . '\',
															\'' . Match::STATE_READYCHECK . '\',
															\'0\',
															\'' . $compo->getId() . '\',
															\'' . $database->real_escape_string($bracketOffset) . '\',
															\'' . $chat->getId() . '\',
															\'' . $database->real_escape_string($bracket) . '\');');

		$match = self::getMatch($database->insert_id);

		$database->close();

		return $match;
	}

	public static function updateMatch(Match $match, $state) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('UPDATE `' . Settings::db_table_infected_compo_matches . '`
																SET `state` = \'' . $database->real_escape_string($state) . '\'
																WHERE `id` = \'' . $match->getId() . '\';');

		$database->close();
	}
}
?>
