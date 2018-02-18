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
require_once 'objects/vote.php';
require_once 'objects/voteoption.php';
require_once 'objects/user.php';
require_once 'objects/compo.php';
require_once 'handlers/compopluginhandler.php';

class VoteHandler {
	/*
	 * Get a vote by the internal id.
	 */
	public static function getVote(int $id): ?Vote {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_votes . '`
																WHERE `id` = \'$id\';');

		return $result->fetch_object('Vote');
	}

	public static function getNumBanned(int $matchId): int {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `id` FROM `' . DatabaseConstants::db_table_infected_compo_votes . '`
																WHERE `consumerId` = ' . $database->real_escape_string($matchId) . ';');

		return $result->num_rows;
	}

	public static function getCurrentBanner(int $numBanned, Match $match): int {
    $plugin = CompoPluginHandler::getPluginObjectOrDefault($match->getCompo()->getPluginName());

    $turnArray = $plugin->getTurnArray($match);

    return $turnArray[$numBanned];
	}

	public static function getCurrentTurnMask(int $numBanned, Match $match): int {
    $plugin = CompoPluginHandler::getPluginObjectOrDefault($match->getCompo()->getPluginName());

    $turnArray = $plugin->getTurnMask($match);

    return $turnArray[$numBanned];
	}

	// Again, consumer is a match id
	public static function banMap(VoteOption $voteOption, int $consumer, int $type) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_votes . '` (`consumerId`, `voteOptionId`,`type`)
											VALUES (\'' . $database->real_escape_string($consumer) . '\',
															\'' . $voteOption->getId() . '\',
															\'' . $database->real_escape_string($type) . '\');');
	}
}
?>
