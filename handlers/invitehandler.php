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
require_once 'objects/invite.php';
require_once 'objects/user.php';
require_once 'objects/clan.php';

class InviteHandler {
	/*
	 * Get a invite by the internal id.
	 */
	public static function getInvite($id) {
		$database = Database::open(Settings::db_name_infected_compo);
		
		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` 
									WHERE `id` = \'' . $id . '\';');
		
		$database->close();
		
			return $result->fetch_object('Invite');
	}
	
	/*
	 * Get all invites.
	 */
	public static function getInvites() {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `'  . Settings::db_table_infected_compo_invites . '`;');
		
		$database->close();

		$inviteList = array();

		while ($object = $result->fetch_object('Invite')) {
			array_push($inviteList, $object);
		}

		return $inviteList;
	}

	/*
	 * Get all invites for the specified user.
	 */
	public static function getInvitesByUser(User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `'  . Settings::db_table_infected_compo_invites . '` 
									WHERE `userId` = \'' . $user->getId() . '\';');
		
		$database->close();

		$inviteList = array();

		while ($object = $result->fetch_object('Invite')) {
			array_push($inviteList, $object);
		}

		return $inviteList;
	}

	/*
	 * Get all invites for a clan.
	 */
	public static function getInvitesByClan(Clan $clan) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '` 
									WHERE `clanId` = \'' . $clan->getId() . '\';');
		
		$database->close();

		$inviteList = array();

		while ($object = $result->fetch_object('Invite')) {
			array_push($inviteList, $object);
		}

		return $userList;
	}

	/*
	 * Invite the specified user to the specifed clan.
	 */
	public static function createInvite(Clan $clan, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_invites . '` (`eventId`, `userId`, `clanId`) 
						  VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\', 
								  \'' . $user->getId() . '\', 
								  \'' . $clan->getId() . '\');');

		$database->close();
	}
}
?>