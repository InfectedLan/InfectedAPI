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
require_once 'handlers/chathandler.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/clan.php';
require_once 'objects/user.php';
require_once 'objects/event.php';
require_once 'objects/compo.php';

class ClanHandler {
	const STATE_MAIN_PLAYER = 0;
	const STATE_STEPIN_PLAYER = 1;

	/*
	 * Get a clan by the internal id.
	 */
	public static function getClan($id) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('Clan');
	}

	/*
	 * Get clan for a specified user. Note that as of 20 august 2015, this on only works on clans for current event.
	 */
	public static function getClansByUser(User $user) {
		$event = EventHandler::getCurrentEvent();
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '`
																WHERE `id` IN (SELECT `clanId` FROM `' . Settings::db_table_infected_compo_memberof . '`
																						  WHERE `userId` = \'' . $user->getId() . '\');');

		$database->close();

		$clanList = [];

		while ($object = $result->fetch_object('Clan')) {
			if ($event->equals($object->getEvent())) {
				$clanList[] = $object;
			}
		}

		return $clanList;
	}

	/*
	 * Get clans for specified compo.
	 */
	public static function getClansByCompo(Compo $compo) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '`
																WHERE `id` IN (SELECT `clanId` FROM `' . Settings::db_table_infected_compo_participantof . '`
																			  			 WHERE `compoId` = \'' . $compo->getId() . '\');');

		$database->close();

		$clanList = [];

		while ($object = $result->fetch_object('Clan')) {
			$clanList[] = $object;
		}

		return $clanList;
	}

    public static function getCompleteClansByCompo(Compo $compo) {
        return self::getQualifiedClansByCompo($compo);
    }
	public static function getQualifiedClansByCompo(Compo $compo) {
		$clanList = [];

		foreach (self::getClansByCompo($compo) as $clan) {
			if (self::isQualified($clan, $compo)) {
				$clanList[] = $clan;
			}
		}

		return $clanList;
	}

    public static function isQualified(Clan $clan, Compo $compo) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantof . '` WHERE `clanId` = \'' . $clan->getId() . '\' AND `compoId` = \'' . $compo->getId() . '\' AND `qualified` = 1;');

        $database->close();

        return $result->num_rows > 0;
    }

    public static function setQualified(Clan $clan, $state) {
        $database = Database::open(Settings::db_name_infected_compo);

        $database->query('UPDATE `' . Settings::db_table_infected_compo_participantof . '` SET `qualified`=' . ($state ? 1 : 0) . '  WHERE `clanId` = \'' . $clan->getId() . '\';');

	//Cleanup just to be sure
        $database->query('DELETE FROM `' . Settings::db_table_infected_compo_qualificationQueue . '` WHERE `clan` = \'' . $clan->getId() . '\';');
        $database->close();
    }

	/*
	 * Get members for specified clan.
	 */
	public static function getClanMembers(Clan $clan) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` IN (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberof . '`
																			   			 WHERE `clanId` = \'' . $clan->getId() . '\');');

		$database->close();

		$memberList = [];

		while ($object = $result->fetch_object('User')) {
			$memberList[] = $object;
		}

		return $memberList;
	}

    /*
	 * Faster way of getting amount of clam members
	 */
	public static function getClanMemberCount(Clan $clan) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` IN (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberof . '`
																			   			 WHERE `clanId` = \'' . $clan->getId() . '\');');

		$count = $result->num_rows;

		$database->close();

		return $count;
	}

	/*
	 * Get playing members for specified clan.
	 */
	public static function getPlayingClanMembers(Clan $clan) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` IN (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberof . '`
																						   WHERE `clanId` = \'' . $clan->getId() . '\'
																						   AND `stepInId` = \'0\');');

		$database->close();

		$memberList = [];

		while ($object = $result->fetch_object('User')) {
			$memberList[] = $object;
		}

		return $memberList;
	}

	/*
	 * Get step in members for specified clan.
	 */
	public static function getStepInClanMembers(Clan $clan) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` IN (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberof . '`
																						   WHERE `clanId` = \'' . $clan->getId() . '\'
																						   AND `stepInId` = \'1\');');

		$database->close();

		$memberList = [];

		while ($object = $result->fetch_object('User')) {
			$memberList[] = $object;
		}

		return $memberList;
	}

	/*
	 * Returns true of the specified user is member of the specified clan.
	 */
	public static function isClanMember(Clan $clan, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_memberof . '`
																WHERE `clanId` = \'' . $clan->getId() . '\'
																AND `userId` = \'' . $user->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Return true if the specified user is a stepin member.
	 */
	public static function isStepInClanMember(Clan $clan, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_memberof . '`
																WHERE `clanId` = \'' . $clan->getId() . '\'
																AND `userId` = \'' . $user->getId() . '\'
																AND `stepInId` = \'1\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Set the step in state of a member.
	 */
	public static function setStepInClanMemberState(Clan $clan, User $user, $state) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('UPDATE `' . Settings::db_table_infected_compo_memberof . '`
																SET `stepInId` = \'' . $database->real_escape_string($state) . '\'
																WHERE `clanId` = \'' . $clan->getId() . '\'
																AND `userId` = \'' . $user->getId() . '\';');

		$database->close();
	}

	/*
	 * Create a new clan.
	 */
	public static function createClan(Event $event, $name, $tag, Compo $compo, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_clans . '` (`eventId`, `chiefId`, `name`, `tag`)
										  VALUES (\'' . $event->getId() . '\',
														  \'' . $user->getId() . '\',
														  \'' . $database->real_escape_string(htmlentities($name, ENT_QUOTES, 'UTF-8')) . '\',
														  \'' . $database->real_escape_string(htmlentities($tag, ENT_QUOTES, 'UTF-8')) . '\');');

		// Fetch the id of the clan we just added.
		$clan = self::getClan($database->insert_id);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_participantof . '` (`clanId`, `compoId`, `qualified`)
										  VALUES (\'' . $database->real_escape_string($clan->getId()) . '\',
												  		\'' . $compo->getId() . '\', \'0\');');

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`clanId`, `userId`, `stepinId`)
										  VALUES (\'' . $database->real_escape_string($clan->getId()) . '\',
												  		\'' . $user->getId() . '\', 0);');

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_clans . '`
																WHERE `id` = \'' . $database->real_escape_string($clan->getId()) . '\';');

		$database->close();

		// Allow user to talk in global chat.
		$mainChat = ChatHandler::getChat(1); // TODO: Change this to the first chat in the array? <- Hmm. Good question.
		$mainChat->addMember($user);

		return $clan;
	}

	/*
	 * Update the specified clan.
	 */
	public static function updateClan(Clan $clan, $name, $tag) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('UPDATE `' . Settings::db_table_infected_compo_clans . '`
										  SET `name` = \'' . $database->real_escape_string($name) . '\',
											  	`tag` = \'' . $database->real_escape_string($tag) . '\'
										  WHERE `id` = \'' . $clan->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove the specified clan.
	 */
	public static function removeClan(Clan $clan) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_clans . '`
						  				WHERE `id` = \'' . $clan->getId() . '\';');
        $database->query('DELETE FROM `' . Settings::db_table_infected_compo_invites . '` WHERE `clanId` = \'' . $clan->getId() . '\';');

		$database->close();
	}

	/*
	 * Kick a specified member from specified clan.
	 */
	public static function kickFromClan(Clan $clan, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('DELETE FROM `' . Settings::db_table_infected_compo_memberof . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `clanId` = \'' . $clan->getId() . '\';');

		$database->close();
	}

    /*
     * Add a clan to the list of clans that are waiting for qualification(if a compo is full)
     */
    public static function addToQualificationQueue(Clan $clan) {
        $compo = $clan->getCompo();

        $database = Database::open(Settings::db_name_infected_compo);

        $database->query('INSERT INTO `' . Settings::db_table_infected_compo_qualificationQueue . '` (`clan`, `compo`, `time`) VALUES (\'' . $clan->getId() . '\', \'' . $compo->getId() . '\', \'' . date('Y-m-d H:i:s') . '\');');

        $database->close();
    }

    /*
     * Remove a clan from the list of clans that are waiting for qualification
     */
    public static function removeFromQualificationQueue(Clan $clan) {
        $database = Database::open(Settings::db_name_infected_compo);

        $database->query('DELETE FROM `' . Settings::db_table_infected_compo_qualificationQueue . '` WHERE `clan` = \'' . $clan->getId() . '\';');

        $database->close();
    }

    public static function isInQualificationQueue(Clan $clan) {
        $database = Database::open(Settings::db_name_infected_compo);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_qualificationQueue . '` WHERE `clan` = \'' . $clan->getId() . '\';');

        $database->close();

        return $result->num_rows > 0;
    }
}
?>
