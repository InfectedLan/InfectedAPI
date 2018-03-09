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

require_once 'handlers/nfccardhandler.php';
require_once 'handlers/nfcunithandler.php';
require_once 'objects/nfccard.php';
require_once 'objects/nfcunit.php';
require_once 'objects/nfclogentry.php';
require_once 'settings.php';
require_once 'database.php';

class NfcLogHandler {
    
    public static function getLogEntry(int $id): NfcLogEntry {
        $database = Database::getConnection(Settings::db_name_infected_tech);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tech_nfclog . '` WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

        return $result->fetch_object('NfcLogEntry');
    }

    public static function createLogEntry(NfcCard $card, NfcUnit $unit, bool $legalPass): NfcLogEntry {
        $database = Database::getConnection(Settings::db_name_infected_tech);

        $database->query('INSERT INTO `' . Settings::db_table_infected_tech_nfclog . '` (`timestamp`, `unitId`, `cardId`, `legalPass`) VALUES (\'' . date('Y-m-d H:i:s') . '\', \'' .
                                                                                                                                                    $database->real_escape_string($unit->getId()) . '\', \'' .
                                                                                                                                                    $database->real_escape_string( $card->getId()) . '\', ' .
                                                                                                                                                    ($legalPass ? 1 : 0) . ');');

        return self::getLogEntry($database->insert_id);
    }

    public static function getLogEntriesByUnit(NfcUnit $unit) : array {
        $database = Database::getConnection(Settings::db_name_infected_tech);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tech_nfclog . '` WHERE `unitId` = ' . $database->real_escape_string($unit->getId()) . ';');

        $entryList = [];

        while($object = $result->fetch_object('NfcLogEntry')) {
            $entryList[] = $object;
        }

        return $entryList;
    }
}
?>
