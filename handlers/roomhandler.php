<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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

require_once 'handlers/roomhandler.php';
require_once 'objects/room.php';
require_once 'settings.php';
require_once 'database.php';

class RoomHandler {

    public static function getRoom(int $id): Room {
        $database = Database::getConnection(Settings::db_name_infected_tech);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tech_rooms . '` WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

        return $result->fetch_object('Room');
    }

    public static function createRoom(String $name, bool $timeLimited): Room {
        $database = Database::getConnection(Settings::db_name_infected_tech);

        $database->query('INSERT INTO `' . Settings::db_table_infected_tech_rooms . '` (`name`, `timeLimited`) VALUES (\'' . $database->real_escape_string($name) . '\', ' . ($timeLimited ? 1 : 0) . ');');

        return self::getRoom($database->insert_id);
    }

    public static function getRooms() : array {
        $database = Database::getConnection(Settings::db_name_infected_tech);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tech_rooms . '`;');

        $entryList = [];

        while($object = $result->fetch_object('Room')) {
            $entryList[] = $object;
        }

        return $entryList;
    }

    public static function getUsersInRoom(Room $room) : array {
        $database = Database::getConnection(Settings::db_name_infected_tech);


    }
}
?>
