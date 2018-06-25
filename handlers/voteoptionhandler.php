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
require_once 'objects/voteoption.php';
require_once 'objects/compo.php';
require_once 'objects/match.php';

class VoteOptionHandler {
	/*
	 * Get a vote option by the internal id.
	 */
	public static function getVoteOption(int $id): ?VoteOption {
		$database = Database::getConnection(Settings::getValue("db_name_infected_compo"));

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_voteoptions . '`
																WHERE `id` = \'' . $id . '\';');

		return $result->fetch_object('VoteOption');
	}

	/*
	 * Get a vote option for a specified compo.
	 */
	public static function getVoteOptionsByCompo(Compo $compo): array {
		$database = Database::getConnection(Settings::getValue("db_name_infected_compo"));

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_voteoptions . '`
																WHERE `compoId` = \'' . $compo->getId() . '\';');

		$voteOptionList = [];

		while ($object = $result->fetch_object('VoteOption')) {
			$voteOptionList[] = $object;
		}

		return $voteOptionList;
	}

	/*
	 * Returns true if specified vote option is voted for the specified match.
	 */
	public static function isVoted(VoteOption $voteOption, Match $match): bool {
		$database = Database::getConnection(Settings::getValue("db_name_infected_compo"));

		$result = $database->query('SELECT `id` FROM `' . DatabaseConstants::db_table_infected_compo_votes . '`
																WHERE `voteOptionId` = \'' . $voteOption->getId() . '\'
																AND `consumerId` = \'' . $match->getId() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns the vote type of the vote option, if any
	 */
	public static function getVoteType(VoteOption $voteOption, Match $match): int {
		$database = Database::getConnection(Settings::getValue("db_name_infected_compo"));

		$result = $database->query('SELECT `type` FROM `' . DatabaseConstants::db_table_infected_compo_votes . '`
																WHERE `voteOptionId` = \'' . $voteOption->getId() . '\'
																AND `consumerId` = \'' . $match->getId() . '\';');

		if ($result->num_rows == 0) {
			return null;
		}

		$row = $result->fetch_row();

		return $row[0];
	}
}
?>
