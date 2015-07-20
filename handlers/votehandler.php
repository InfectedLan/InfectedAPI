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
require_once 'objects/vote.php';
require_once 'objects/voteoption.php';
require_once 'objects/user.php';

class VoteHandler {
	/*
	 * Get a vote by the internal id.
	 */
	public static function getVote($id) {
		$database = Database::open(Settings::db_name_infected_compo);
		
		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_votes . '` 
									WHERE `id` = \'$id\';');
		
		$database->close();
		
		return $result->fetch_object('Vote');
	}
	
	// TODO: Document this.
	public static function getNumBanned(User $consumer) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_votes . '` 
									WHERE `consumerId` = ' . $consumer->getId() . ';');
		
		$database->close();

		return $result->num_rows;
	}

	// TODO: Document this.
	public static function getCurrentBanner($numBanned) {
		$turnArray = array(0, 1, 0, 1, 1, 0, 2);
		
		return $turnArray[$numBanned];
	}

	// TODO: Document this.
	public static function banMap(VoteOption $voteOption, User $consumer) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('INSERT INTO `' . Settings::db_table_infected_compo_votes . '` (`consumerId`, `voteOptionId`) 
									VALUES (\'' . $consumer->getId() . '\', 
											\'' . $voteOption->getId() . '\');');
		
		$database->close();
	}
}
?>