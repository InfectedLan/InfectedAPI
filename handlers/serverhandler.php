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
require_once 'objects/server.php';
require_once 'objects/compo.php';
require_once 'objects/match.php';

class ServerHandler {
	/*
	 * Get a server by internal id
	 */
	public static function getServer($id) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_servers . '`
																WHERE `id` = \'' . $id . '\';');

		$database->close();

		return $result->fetch_object('Server');
	}

	/*
	 * Get a server for a specified compo.
	 */
	public static function getServersByCompo(Compo $compo) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_servers . '`
																WHERE `compoId` = \'' . $compo->getId() . '\';');

		$database->close();

		$serverList = [];

		while ($object = $result->fetch_object('Server')) {
			$serverList[] = $object;
		}

		return $serverList;
	}

    /*
     * Creates a new server entry
     */
	public static function createServer(Compo $compo, $humanName, $connectionDetails) {
	    $database = Database::getConnection(Settings::db_name_infected_compo);

	    $database->query('INSERT INTO `' . Settings::db_table_infected_compo_servers . '`(`compoId`, `humanName`, `connectionDetails`) VALUES (' . $compo->getId() . ', \'' . $database->real_escape_string($humanName) . '\', \'' . $database->real_escape_string($connectionDetails) . '\');');

        $return_id = $database->insert_id;
        
	    $database->close();
        
        return $return_id;
	}

    /*
     * Deletes a server entry
     */
    public static function deleteServer(Server $server) {
        $database = Database::getConnection(Settings::db_name_infected_compo);

        $database->query('DELETE FROM `' . Settings::db_table_infected_compo_servers . '` WHERE `id` = \'' . $server->getId() . '\';');
    }

	/*
	 * Sets the connection details of a server
	 */
	public static function setConnectionDetails(Server $server, $connectionDetails) {
	    $database = Database::getConnection(Settings::db_name_infected_compo);

	    $result = $database->query('UPDATE `' . Settings::db_table_infected_compo_servers . '` SET `connectionDetails` = \'' . $database->real_escape_string($connectionDetails) . '\' WHERE `` = \'' . $server->getId() . '\';');

	    $database->close();
	}

	/*
	 * Sets the human name of a server
	 */
	public static function setHumanName(Server $server, $humanName) {
	    $database = Database::getConnection(Settings::db_name_infected_compo);

	    $result = $database->query('UPDATE `' . Settings::db_table_infected_compo_servers . '` SET `humanName` = \'' . $database->real_escape_string($humanName) . '\' WHERE `` = \'' . $server->getId() . '\';');

	    $database->close();
	}
}
?>
