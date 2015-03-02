/*
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

<?php
require_once 'database.php';
require_once 'settings.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'objects/eventobject.php';

class Invite extends EventObject {
	private $userId;
	private $clanId;

	/*
	 * Returns the user that this invite is for.
	 */ 
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the clan this invite is to.
	 */ 
	public function getClan() {
		return ClanHandler::getClan($this->clanId);
	}

	/*
	 * Decline this invite.
	 */ 
	public function decline() {
		$con = Database::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_compo_invites . '`
							WHERE `id` = \'' . $this->getId() . '\';');
	
		Database::close($con);
	}

	/*
	 * Accept this invite.
	 */ 
	public function accept() {
		$con = Database::open(Settings::db_name_infected_compo);

		mysqli_query($con, 'DELETE FROM `' . Settings::db_table_infected_compo_invites . '`
							WHERE `id` = \'' . $this->getId() . '\';');

		$clan = $this->getClan();

		$memberList = ClanHandler::getPlayingMembers($clan);
		$compo = ClanHandler::getCompo($clan);

		if (count($memberList) < $compo->getTeamSize()) {
			$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`userId`, `clanId`, `stepInId`) 
							  VALUES (' . $this->getUser()->getId() . ', 
									  ' . $clan->getId() . ', 0);');
		} else {
			$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`userId`, `clanId`, `stepInId`) 
							  VALUES (\'' . $this->getUser()->getId() . '\', 
									  \'' . $clan->getId() . '\', 
									  \'1\');');
		}

		$database->close();
	}
}
?>