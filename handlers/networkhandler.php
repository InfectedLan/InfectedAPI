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
require_once 'database.php';
require_once 'objects/network.php';
require_once 'objects/networktype.php';

class NetworkHandler {
	/*
	 * Returns the network with the given id.
	 */
	public static function getNetwork(int $id): ?Network {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_networks . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Network');
	}

	/*
	 * Returns a list of all networks.
	 */
	public static function getNetworks(): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_networks . '`;');

		$networkList = [];

		while ($object = $result->fetch_object('Network')) {
			$networkList[] = $object;
		}

		return $networkList;
	}

	/*
	 * Create a new network.
	 */
	public static function createNetwork(string $name, string $description, int $vlanId = 0, bool $fallback = false): Network {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('INSERT INTO `' . Settings::db_table_infected_networks . '` (`name`, `description`, `vlanId`, `fallback`)
										  VALUES (\'' . $database->real_escape_string($name) . '\',
														  \'' . $database->real_escape_string($description) . '\',
														  \'' . $database->real_escape_string($vlanId) . '\',
															\'' . $database->real_escape_string($fallback) . '\');');

		return self::getNetwork($database->insert_id);
	}

	/*
	 * Update a network.
	 */
	public static function updateNetwork(Network $network, string $name, ?string $description, int $vlanId = 0, bool $fallback = false) {
	  $database = Database::getConnection(Settings::db_name_infected);

		$database->query('UPDATE `' . Settings::db_table_infected_networks . '`
										  SET `name` = \'' . $database->real_escape_string($name) . '\',
												  `description` = \'' . $database->real_escape_string($description) . '\',
												  `vlanId` = \'' . $database->real_escape_string($vlanId) . '\',
													`fallback` = \'' . $database->real_escape_string($fallback) . '\'
										  WHERE `id` = \'' . $network->getId() . '\';');
	}

	/*
	 * Remove a network.
	 */
	public static function removeNetwork(Network $network) {
		$database = Database::getConnection(Settings::db_name_infected);

		$database->query('DELETE FROM `' . Settings::db_table_infected_networks . '`
						  				WHERE `id` = \'' . $network->getId() . '\';');
	}

////////////////////////////////////////////////////////////////////////////////

	/*
	 * Returns the network type by port type.
	 */
	public static function getNetworkType(int $id): ?NetworkType {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_networktypes . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('NetworkType');
	}

	/*
	 * Returns the network type by port type.
	 */
	public static function getNetworkTypeByPortType(string $portType): ?NetworkType {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_networktypes . '`
																WHERE `portType` = \'' . $database->real_escape_string($portType) . '\';');

		return $result->fetch_object('NetworkType');
	}

////////////////////////////////////////////////////////////////////////////////

	/*
	 * Returns true if the user has network access for the given network type.
	 */
	public static function hasNetworkAccess(User $user, NetworkType $networkType, Event $event = null): bool {
		$database = Database::getConnection(Settings::db_name_infected);

		// What should SQL query do?
		// Return true if: userId = 0 and groupId = null and teamId = null. 													(Default for all Participants). WRITTEN.
		// Return true if: userId = null and groupId = 0 and teamId = null. 													(Default for all Members).

		// Return true if: userId = $user->getId(). 																									(Per user). WRITTEN. Partly
		// Return true if: userId = null and (groupId = $group->getId() and teamId = null).						(Per group).
		// Return true if: userId = null and (groupId = $group->getId() and teamId = $team->getId()). (Per team).

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_networkaccess . '`
																WHERE (`userId` = ' . $user->getId() . '
																       OR (`userId` IS NULL
																       		 AND (`groupId` IN (SELECT `groupId` FROM `' . Settings::db_name_infected . '`.`' . Settings::db_table_infected_crew_memberof . '`
																						                 WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
																						                 AND `userId` = ' . $user->getId() . '
																						                 ORDER BY `groupId` DESC)
																                OR `groupId` = 0)
																           AND (`teamId` IN (SELECT `teamId` FROM `' . Settings::db_name_infected . '`.`' . Settings::db_table_infected_crew_memberof . '`
																					                   WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
																					                   AND `userId` = ' . $user->getId() . '
																					                   ORDER BY `teamId` DESC)
																                OR `teamId` IS NULL))
																       OR (`userId` = 0
																           AND `groupId` IS NULL
																           AND `teamId` IS NULL))
																AND `networkTypeId` = ' . $networkType->getId() . '
																GROUP BY `networkId`
																ORDER BY CASE WHEN `userId` > 0 THEN `userId` END DESC,
																         CASE WHEN `groupId` > 0 THEN `groupId` END DESC,
																         CASE WHEN `teamId` > 0 THEN `teamId` END DESC,
																         CASE WHEN `groupId` = 0 THEN `groupId` END DESC;');

		return $result->num_rows > 0;
	}

	/*
	 * Returns the network by user.
	 */
	public static function getNetworkByUser(User $user, NetworkType $networkType, Event $event = null): Network {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_networks . '`
																WHERE `id` = (SELECT `networkId` FROM `' . Settings::db_table_infected_networkaccess . '`
																              WHERE (`userId` = ' . $user->getId() . '
																                     OR (`userId` IS NULL
																                         AND (`groupId` IN (SELECT `groupId` FROM `' . Settings::db_name_infected . '`.`' . Settings::db_table_infected_crew_memberof . '`
																                                           WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
																                                           AND `userId` = ' . $user->getId() . '
																                                           ORDER BY `groupId` DESC)
																                              OR `groupId` = 0)
																                         AND (`teamId` IN (SELECT `teamId` FROM `' . Settings::db_name_infected . '`.`' . Settings::db_table_infected_crew_memberof . '`
																                                           WHERE `eventId` = ' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '
																                                           AND `userId` = ' . $user->getId() . '
																                                           ORDER BY `teamId` DESC)
																                              OR `teamId` IS NULL))
																                     OR (`userId` = 0
																                         AND `groupId` IS NULL
																                         AND `teamId` IS NULL))
																              AND `networkTypeId` = ' . $networkType->getId() . '
																							GROUP BY `networkId`
																              ORDER BY CASE WHEN `userId` > 0 THEN `userId` END DESC,
																                       CASE WHEN `groupId` > 0 THEN `groupId` END DESC,
																                       CASE WHEN `teamId` > 0 THEN `teamId` END DESC,
																                       CASE WHEN `groupId` = 0 THEN `groupId` END DESC
																              LIMIT 1);');

		/*
		if ($user->isGroupMember()) {
			if ($user->isTeamMember()) {
				$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_networks . '`
																		WHERE `id` = (SELECT `networkId` FROM `'. Settings::db_table_infected_networkaccess . '`
																									WHERE `userId` = \'' . $user->getId() . '\'
																									OR ((`teamId` = \'' . $user->getTeam()->getId() . '\' OR `teamId` = \'0\') AND `userId` IS NULL)
																									AND `networkTypeId` = \'' . $networkType->getId() . '\'
																									LIMIT 1);');
			} else {
				$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_networks . '`
																		WHERE `id` = (SELECT `networkId` FROM `'. Settings::db_table_infected_networkaccess . '`
																									WHERE `userId` = \'' . $user->getId() . '\'
																									OR ((`groupId` = \'' . $user->getGroup()->getId() . '\' OR `groupId` = \'0\') AND `userId` IS NULL)
 																								  AND `teamId` IS NULL
																									AND `networkTypeId` = \'' . $networkType->getId() . '\'
																									LIMIT 1);');
			}
		} else {
			$result = $database->query('SELECT * FROM `'. Settings::db_table_infected_networks . '`
																	WHERE `id` IN (SELECT `networkId` FROM `'. Settings::db_table_infected_networkaccess . '`
																								 WHERE (`userId` = \'' . $user->getId() . '\' OR `userId` = \'0\')
																								 AND `groupId` IS NULL
																								 AND `teamId` IS NULL
																								 AND `networkTypeId` = \'' . $networkType->getId() . '\'
																								 LIMIT 1);');
		}
		*/

		return $result->fetch_object('Network');
	}
}
?>
