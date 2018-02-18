<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'databaseconstants.php';
require_once 'database.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/votehandler.php';
require_once 'handlers/voteoptionhandler.php';
require_once 'handlers/compopluginhandler.php';
require_once 'objects/match.php';
require_once 'objects/compo.php';
require_once 'objects/chat.php';
require_once 'objects/clan.php';
require_once 'objects/user.php';
require_once 'objects/event.php';
require_once 'objects/compoplugin.php';

/*
 * EPIC WARNING:
 *
 * participantOfMatch should only have a clan linked if the clan is ready to play that match
 */
class MatchHandler {
	//State of the 'participantOf' table responsible for the participants of a match
	const PARTICIPANTOF_STATE_CLAN = 0;
	const PARTICIPANTOF_STATE_WINNER = 1;
	const PARTICIPANTOF_STATE_LOOSER = 2;
	const PARTICIPANTOF_STATE_STEPIN = 3;

	/*
	 * Get a match by the internal id.
	 */
	public static function getMatch(int $id): ?Match {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
								   WHERE `id` = ' . $id . ';');

		return $result->fetch_object('Match');
	}

	/*
	 * Remove a match
	 */
	public static function deleteMatch(Match $match) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
						 WHERE `id` = \'' . $match->getId() . '\';');

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_compo_participantof . '`
						 WHERE `matchId` = \'' . $match->getId() . '\';');

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
						 WHERE `participantId` = \'' . $match->getId() . '\'
						 AND (`type` = \'' . Settings::compo_match_participant_type_match_winner . '\'
							  OR `type` = \'' . Settings::compo_match_participant_type_match_looser . '\');');

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
						 WHERE `matchId` = \'' . $match->getId() . '\';');

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_compo_matchrelationships . '`
						 WHERE `fromCompoId` = \'' . $match->getId() . '\';');

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_compo_matchrelationships . '`
						 WHERE `toCompoId` = \'' . $match->getId() . '\';');
	}

	/*
	 * Returns a list of all matches. Matches are completely naive of what they are connected to, so this will have little use.
	 */
	public static function getMatches(): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`;');

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			$matchList[] = $object;
		}

		return $matchList;
	}

  /*
   * Returns upcoming matches within the next $time period(seconds)
   */
  public static function getUpcomingMatches(int $interval): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
								   WHERE `scheduledTime` >= NOW()
								   AND `scheduledTime` < NOW() + INTERVAL ' . $database->real_escape_string($interval) . ' SECOND;');

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			$matchList[] = $object;
		}

		return $matchList;
	}

  /**
   * Returns current playing matches for current event
   */
  public static function getPlayingMatches(): array {
		$event = EventHandler::getCurrentEvent();
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
								   WHERE `compoId` IN (SELECT `id` FROM `' . DatabaseConstants::db_table_infected_compo_compos . '`
								   					   WHERE `eventId` = ' . $event->getId() . ')
								   AND `winnerId` = 0
								   AND `scheduledTime` < \'' . date('Y-m-d H:i:s') . '\';');

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			$matchList[] = $object;
		}

		return $matchList;
	}

	// Unstable if user has multiple matches happening. Returns the "current match", current being the first match with a scheduled time before now, and without a winner.
	public static function getMatchByUser(User $user): ?Match {
		$clanList = ClanHandler::getClansByUser($user);

		foreach ($clanList as $clan) {
			$match = self::getMatchByClan($clan);

			if ($match != null) {
				return $match;
			}
		}

		return null;
	}

	public static function getMatchByClan(Clan $clan): ?Match {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `matchId` FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
								   WHERE `type` = \'' . Settings::compo_match_participant_type_clan . '\'
								   AND `participantId` = ' . $clan->getId() . ';');

		while ($row = $result->fetch_array()) {
			$match = self::getMatch($row['matchId']);

			if ($match->getWinner() == 0 && $match->getScheduledTime() < time()) {
				return $match;
			}
		}

		return null;
	}

	public static function getPendingMatchesByCompo(Compo $compo): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
								   WHERE `compoId` = ' . $compo->getId() . ';');

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			if ($object->getScheduledTime() > time() ||
				!self::isReady($object)) {
				$matchList[] = $object;
			}
		}

		return $matchList;
	}

	public static function getCurrentMatchesByCompo(Compo $compo): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		//Picks matches that 'should' be running.
		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
								   WHERE `compoId` = ' . $compo->getId() . '
								   AND `winnerId` = 0
								   AND `scheduledTime` < \'' . date('Y-m-d H:i:s') . '\';');

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			if (self::isReady($object)) {
				$matchList[] = $object;
			}
		}

		return $matchList;
	}

	public static function getFinishedMatchesByCompo(Compo $compo): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		// Picks matches that 'should' be running.
		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
								   WHERE `compoId` = ' . $compo->getId() . '
								   AND `winnerId` != 0;');

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			$matchList[] = $object;
		}

		return $matchList;
	}

	public static function getMatchesByCompo(Compo $compo): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
								   WHERE `compoId` = \'' . $compo->getId() . '\';');

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			$matchList[] = $object;
		}

		return $matchList;
	}

	public static function hasMatchByClan(Clan $clan): bool {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `id` FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
								   WHERE `id` IN (SELECT `matchId` FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
												  WHERE `type` = \'' . Settings::compo_match_participant_type_clan . '\'
												  AND `participantId` = \'' . $clan->getId() . '\');');

		return $result->num_rows > 0;
	}

	public static function addMatchParticipant(int $type, int $participantId, Match $match) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '` (`type`, `participantId`, `matchId`)
						 VALUES (' . $type . ',
						   	     ' . $participantId . ',
								 ' . $match->getId() . ');');

		//Add members of clan to chat(if clan)
		if ($type == self::PARTICIPANTOF_STATE_CLAN) {
			$result = $database->query('SELECT `userId` FROM `' . DatabaseConstants::db_table_infected_compo_memberof . '`
									   WHERE `clanId` = ' . $database->real_escape_string($participantId) . ';');

			while($row = $result->fetch_array()) {
				ChatHandler::addChatMemberById($match->getChatId(), $row["userId"]);
		  }
		}

		if ($type != self::PARTICIPANTOF_STATE_CLAN &&
			$type != self::PARTICIPANTOF_STATE_LOOSER) {
			$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_matchrelationships . '` (`fromCompoId`, `toCompoId`)
							 VALUES (' . $database->real_escape_string($participantId) . ',
							  		 ' . $match->getId() . ');');
		}
	}

	public static function setWinner(Match $match, Clan $clan, CompoPlugin $compoPlugin = null) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		// Set winner of match
		$database->query('UPDATE `' . DatabaseConstants::db_table_infected_compo_matches . '`
						 SET `winnerId` = ' . $clan->getId() . '
						 WHERE `id` = ' . $match->getId() . ';');

		// Update match results
		//First, get list of matches we want to change
		$toWinList = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
									  WHERE `type` = 1
									  AND `participantId` = ' . $match->getId() . ';');

		while ($row = $toWinList->fetch_array()) {
			$database->query('UPDATE `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
											  SET `type` = \'0\',
												  	`participantId` = \'' . $clan->getId() . '\'
											  WHERE `id` = \'' . $row['id'] . '\';');

			$checkingMatchId = MatchHandler::getMatch($row['matchId']);
			ChatHandler::addChatMembers($checkingMatchId->getChat(), $clan->getMembers());
		}

		/*
		$database->query('UPDATE `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
							SET `type` = 0, `participantId` = ' . $database->real_escape_string($clan->getId()) . '
							WHERE `type` = 1
							AND `participantId` = ' . $database->real_escape_string($match->getId()) . ';');
		*/
		$looser = null;

		$participantList = self::getParticipantsByMatch($match);

		foreach ($participantList as $participant) {
			if ($participant->getId() != $clan->getId()) {
				$looser = $participant;
			}
		}

		$toLooseList = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
																		 WHERE `type` = \'2\'
																		 AND `participantId` = \'' . $match->getId() . '\';');

		while ($row = $toLooseList->fetch_array()) {
			$database->query('UPDATE `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
											  SET `type` = \'0\',
												  	`participantId` = \'' . $looser->getId() . '\'
											  WHERE `id` = \'' . $row['id'] . '\';');

			$checkingMatchId = MatchHandler::getMatch($row['matchId']);
			ChatHandler::addChatMembers($checkingMatchId->getChat(), $looser->getMembers());
		}

		//Notify the compo plugin that the match is over.
		if ($compoPlugin == null) {
			$compoPlugin = CompoPluginHandler::getPluginObjectOrDefault($match->getCompo()->getPluginName());
		}

		$compoPlugin->onMatchFinished($match);
	}

	public static function getParticipantsByMatch(Match $match): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_clans . '`
																WHERE `id` IN (SELECT `participantId` FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
																						   WHERE `matchId` = \'' . $match->getId() . '\'
																						   AND `type` = \'' . Settings::compo_match_participant_type_clan . '\');');

		$clanList = [];

		while ($object = $result->fetch_object('Clan')) {
			$clanList[] = $object;
		}


		return $clanList;
	}

  public static function removeParticipantEntry(int $id) {
    //First, we need to fetch it, as there are some extra steps we need to take in some situations
    $database = Database::getConnection(Settings::db_name_infected_compo);

    $database->query('SELECT `type` FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
											WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
  }

  /*
   * Some times, you want low level data on the participants.
   */
	public static function getParticipantData(Match $match): array {
    $database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
																WHERE `matchId` = \'' . $match->getId() . '\';');

		$rawData = [];

		while ($row = $result->fetch_array()) {
      array_push($rawData, ['id' => $row['id'],
													  'type' => $row['type'],
													  'participantId' => $row['participantId'],
												 	  'matchId' => $row['matchId']]);
		}

		return $rawData;
	}

	public static function getParticipantStringByMatch(Match $match): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
																WHERE `matchId` = \'' . $match->getId() . '\';');

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

	public static function getParticipantTagsByMatch(Match $match): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
																WHERE `matchId` = \'' . $match->getId() . '\';');

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

  /**
   * Returns an array with "hunan readable" string representations of the participants
   */
	public static function getParticipantsJsonByMatch(Match $match): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
																WHERE `matchId` = \'' . $match->getId() . '\';');

		$jsonArray = [];

		while ($row = $result->fetch_array()) {
			if ($row['type'] == Settings::compo_match_participant_type_clan) {
				$clan = ClanHandler::getClan($row['participantId']);
				array_push($jsonArray, array('type' => $row['type'], 'id' => $row['participantId'], 'value' => $clan->getName() . ' - ' . $clan->getTag() . ''));
      } elseif ($row['type'] == Settings::compo_match_participant_type_match_winner) {
        array_push($jsonArray, array('type' => $row['type'], 'id' => $row['participantId'], 'value' => "Winner of match " . $row['participantId']));
      } elseif ($row['type'] == Settings::compo_match_participant_type_match_looser) {
				array_push($jsonArray, array('type' => $row['type'], 'id' => $row['participantId'], 'value' => "Looser of match " . $row['participantId']));
      } elseif ($row['type'] == Settings::compo_match_participant_type_match_walkover) {
        array_push($jsonArray, array('type' => $row['type'], 'value' => "Walkover"));
			} else {
				array_push($jsonArray, array('type' => $row['type'], 'value' => $row['participantId'] . "(error)"));
			}
		}

		return $jsonArray;
	}

	public static function getMatchParents(Match $match): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
																WHERE `id` IN (SELECT `fromCompoId` FROM `' . DatabaseConstants::db_table_infected_compo_matchrelationships . '`
																			  			WHERE `toCompoId` = \'' . $match->getId() . '\');');

		$matchList = array();

		while ($object = $result->fetch_object('Match')) {
			array_push($matchList, $object);
		}

		return $matchList;
	}

  public static function getMatchChildren(Match $match): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matches . '`
																WHERE `id` IN (SELECT `toCompoId` FROM `' . DatabaseConstants::db_table_infected_compo_matchrelationships . '`
																			  			 WHERE `fromCompoId` = \'' . $match->getId() . '\');');

		$matchList = [];

		while ($object = $result->fetch_object('Match')) {
			$matchList[] = $object;
		}

		return $matchList;
	}

	// Checks if the match can run(If we have enough participants. Returns false if we have to wait for earlier matches to complete)
	public static function isReady(Match $match): bool {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `type` FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
																WHERE `matchId` = \'' . $match->getId() . '\';');

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
	public static function isUserReady(User $user, Match $match): bool {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_readyusers . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `matchId` = \'' . $match->getId() . '\';');

		return $result->num_rows > 0;
	}

	public static function getReadyCount(Match $match): int {
    $count = 0;
    $participantClans = self::getParticipantsByMatch($match);

		foreach ($participantClans as $clan) {
			$members = $clan->getMembers();

			foreach ($members as $member) {
		    if (self::isUserReady($member, $match)) {
					$count++;
		    }
			}
    }

    return $count;
	}

	public static function acceptMatch(User $user, Match $match) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_readyusers . '` (`userId`, `matchId`)
											VALUES (\'' . $user->getId() . '\',
															\'' . $match->getId() . '\');');
	}

	public static function allHasAccepted(Match $match): bool {
    $database = Database::getConnection(Settings::db_name_infected_compo);

    $result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_participantofMatch . '`
															WHERE `type` = \'0\'
															AND `matchId` = \'' . $match->getId() . '\';');

    // Iterate through clans
    while ($row = $result->fetch_array()) {
			$users = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_memberof . '`
																   WHERE `clanId` = \'' . $row['participantId'] . '\' AND `stepinId` = 0;');

			while ($userRow = $users->fetch_array()) {
		    $userCheck = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_readyusers . '`
																		   WHERE `userId` = \'' . $userRow['userId'] . '\'
																		   AND `matchId` = \'' . $match->getId() . '\';');

		    $row = $userCheck->fetch_array();

		    if (!$row) {
					return false;
		    }
			}
    }

	  return true;
	}

	public static function createMatch(int $scheduledTime, string $connectData, Compo $compo, int $bracketOffset, Chat $chat, bool $bracket): Match {
		$database = Database::getConnection(Settings::db_name_infected_compo);

    $database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_matches . '` (`scheduledTime`, `connectDetails`, `state`, `winnerId`, `compoId`, `bracketOffset`, `chatId`, `bracket`)
											VALUES (\'' . date('Y-m-d H:i:s', $scheduledTime) . '\',
															\'' . $database->real_escape_string($connectData) . '\',
															\'' . Match::STATE_READYCHECK . '\',
															\'0\',
															\'' . $compo->getId() . '\',
															\'' . $database->real_escape_string($bracketOffset) . '\',
															\'' . $chat->getId() . '\',
															\'' . $database->real_escape_string($bracket) . '\');');

		return self::getMatch($database->insert_id);
	}

  public static function setTime(Match $match, int $time) {
    $database = Database::getConnection(Settings::db_name_infected_compo);

    $database->query('UPDATE `' . DatabaseConstants::db_table_infected_compo_matches . '`
											SET `scheduledTime` = \'' . date('Y-m-d H:i:s', $time) . '\'
											WHERE `id` = \'' . $match->getId() . '\';');
  }

	public static function updateMatch(Match $match, int $state) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('UPDATE `' . DatabaseConstants::db_table_infected_compo_matches . '`
											SET `state` = \'' . $database->real_escape_string($state) . '\'
											WHERE `id` = \'' . $match->getId() . '\';');
	}

	public static function updateConnectDetails(Match $match, $connectDetails) {
	  $database = Database::getConnection(Settings::db_name_infected_compo);

	  $database->query('UPDATE `' . DatabaseConstants::db_table_infected_compo_matches . '`
											SET `connectDetails` = \'' . $database->real_escape_string($connectDetails) . '\'
											WHERE `id` = \'' . $match->getId() . '\';');
	}

  public static function getMetadata(Match $match): array {
    $database = Database::getConnection(Settings::db_name_infected_compo);

    $result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matchmetadata . '`
																WHERE `match` = \'' . $match->getId() . '\';');

    $metadata = [];

    while ($row = $result->fetch_array()) {
        $metadata[$row['key']] = $row['value'];
    }

    return $metadata;
  }

  public static function setMetadata(Match $match, string $key, string $value) {
    $database = Database::getConnection(Settings::db_name_infected_compo);

    if (self::hasKey($match, $key)) {
    	$database->query('UPDATE `' . DatabaseConstants::db_table_infected_compo_matchmetadata . '`
												SET `value` = \'' . $database->real_escape_string($value) .'\'
												WHERE `key` = \'' . $database->real_escape_string($key) . '\'
												AND `match` = \'' . $match->getId() . '\';');
    } else {
      $database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_matchmetadata . '` (`match`, `key`, `value`)
												VALUES (\'' . $match->getId() . '\',
																\'' . $database->real_escape_string($key) . '\',
																\'' . $database->real_escape_string($value) . '\');');
    }
  }

  public static function hasKey(Match $match, string $key): bool {
    $database = Database::getConnection(Settings::db_name_infected_compo);

    $result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_matchmetadata . '`
																WHERE `match` = \'' . $match->getId() . '\'
																AND `key` = \'' . $database->real_escape_string($key) . '\';');

    return $result->num_rows > 0;
  }

  public static function getJsonableData(Match $match): array {
		$matchData['id'] = $match->getId();
    $matchData['state'] = $match->getState();
    $matchData['ready'] = $match->isReady();
    $matchData['compoId'] = $match->getCompo()->getId();
    $matchData['currentTime'] = time();
    $matchData['startTime'] = $match->getScheduledTime();
    $matchData['chatId'] = $match->getChat()->getId();

    if ($match->getState() == Match::STATE_READYCHECK && $match->isReady()) {
      $readyData = [];

      foreach (MatchHandler::getParticipantsByMatch($match) as $clan) {
        $memberData = [];

        foreach ($clan->getPlayingMembers() as $member) {
          $avatarFile = null;

          if ($member->hasValidAvatar()) {
            $avatarFile = $member->getAvatar()->getThumbnail();
          } else {
            $avatarFile = AvatarHandler::getDefaultAvatar($member);
          }

          $memberReadyStatus = ['userId' => $member->getId(),
                                'nick' => $member->getNickname(),
                                'avatarUrl' => $avatarFile,
                                'ready' => MatchHandler::isUserReady($member, $match)];

          $memberData[] = $memberReadyStatus;
        }

        $clanData = ['clanName' => $clan->getName(),
                     'clanTag' => $clan->getTag(),
                     'members' => $memberData];

        $readyData[] = $clanData;
      }

      $matchData['readyData'] = $readyData;
    } else if ($match->getState() == Match::STATE_CUSTOM_PREGAME && $match->isReady()) {
      $matchData['banData'] = self::getBanData($match);
    } else if ($match->getState() == Match::STATE_JOIN_GAME && $match->isReady()) {
      $gameData = [];
      $gameData['connectDetails'] = $match->getConnectDetails();
      $clanList = [];

      foreach (MatchHandler::getParticipantsByMatch($match) as $clan) {
        $clanData = [];
        $clanData['clanName'] = $clan->getName();
        $clanData['clanTag'] = $clan->getTag();
        $memberData = [];

        foreach ($clan->getMembers() as $member) {
          $userData = [];

          $userData['userId'] = $member->getId();
          $userData['nick'] = $member->getNickname();
          $userData['chief'] = $member->equals($clan->getChief());

          $memberData[] = $userData;
        }

        $clanData['members'] = $memberData;
        $clanList[] = $clanData;
      }

      $gameData['clans'] = $clanList;
      $compo = $match->getCompo();
	    $plugin = CompoPluginHandler::getPluginObjectOrDefault($compo->getPluginName());
	    $tempMapData = [];

      if ($plugin->hasVoteScreen()) {
				$options = VoteOptionHandler::getVoteOptionsByCompo($compo);

				foreach ($options as $option) {
	    		$type = VoteOptionHandler::getVoteType($option, $match);

					if ($type == 1) {
            $mapData = [];

            $mapData['name'] = $option->getName();
            $mapData['thumbnail'] = $option->getThumbnailUrl();

            array_push($tempMapData, $mapData);
          }
      	}

				foreach ($options as $option) {
          if (!VoteOptionHandler::isVoted($option, $match)) {
            $mapData = [];

            $mapData['name'] = $option->getName();
            $mapData['thumbnail'] = $option->getThumbnailUrl();

            array_push($tempMapData, $mapData);
          }
        }
      }

    	$gameData['mapData'] = $tempMapData;
      $matchData['gameData'] = $gameData;
		}

		return $matchData;
  }

  public static function getBanData(Match $match): array {
		$banData = [];
		$bannableMapsArray = [];

		foreach (VoteOptionHandler::getVoteOptionsByCompo($match->getCompo()) as $voteOption) {
	    $optionData = [];
	    $optionData['name'] = $voteOption->getName();
	    $optionData['thumbnailUrl'] = $voteOption->getThumbnailUrl();
	    $optionData['id'] = $voteOption->getId();
	    $optionData['isSelected'] = VoteOptionHandler::isVoted($voteOption, $match);
	    $optionData['selectionType'] = VoteOptionHandler::getVoteType($voteOption, $match);
	    $bannableMapsArray[] = $optionData;
		}

		$banData['options'] = $bannableMapsArray;
		$numBanned = VoteHandler::getNumBanned($match->getId());
		$banData['turn'] = VoteHandler::getCurrentBanner($numBanned, $match);
		$banData['selectType'] = VoteHandler::getCurrentTurnMask($numBanned, $match) == 0 ? "banne" : "picke";

		$clanList = [];

		foreach (self::getParticipantsByMatch($match) as $clan) {
	    $clanData = ['name' => $clan->getName(),
			 						 'tag' => $clan->getTag()];

	    /*
	    $memberData = [];

	    foreach ($clan->getMembers() as $member) {
				$userData = ['userId' => $member->getId(),
				     'nick' => $member->getNickname(),
				     'chief' => $member->equals($clan->getChief())];

				$memberData[] = $userData;
	    }

	    $clanData['members'] = $memberData;
			*/

	    $clanList[] = $clanData;
		}

		$banData['clans'] = $clanList;

		return $banData;
  }
}
?>
