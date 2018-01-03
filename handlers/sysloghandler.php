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
require_once 'objects/syslogentry.php';

class SyslogHandler {
  const SEVERITY_INFO = 1; // Not dangerous, but informational
  const SEVERITY_ISSUE = 2; // Someone should check out this
  const SEVERITY_WARNING = 3; // Calm before the storm
  const SEVERITY_CRITICAL = 4; // HOLY FUCK THE SERVERS ARE BURNING

  public static function getSyslogEntry(int $id): ?SyslogEntry {
    $database = Database::getConnection(Settings::db_name_infected);

    $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_syslogs . '`
															  WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

    return $result->fetch_object('SyslogEntry');
  }

  public static function getLastEntries(int $count): array {
  	$database = Database::getConnection(Settings::db_name_infected);

  	$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_syslogs . '`
                                ORDER BY `id` DESC
                                LIMIT ' . $database->real_escape_string($count) . ';');

  	$syslogList = [];

  	while ($object = $result->fetch_object('SyslogEntry')) {
  	    $syslogList[] = $object;
  	}

  	return $syslogList;
  }

  public static function getLastEntriesBySource(string $source, int $count): array {
  	$database = Database::getConnection(Settings::db_name_infected);

  	$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_syslogs . '`
                                WHERE `source` LIKE \'' . $database->real_escape_string($source) . '\'
                                ORDER BY `id` DESC
                                LIMIT ' . $database->real_escape_string($count) . ';');

  	$syslogList = [];

  	while ($object = $result->fetch_object('SyslogEntry')) {
  	    $syslogList[] = $object;
  	}

  	return $syslogList;
  }

  public static function log(string $message, string $source, User $user = null, int $severity = self::SEVERITY_INFO, array $metadata = []) {
  	$database = Database::getConnection(Settings::db_name_infected);

    $database->query('INSERT INTO `' . Settings::db_table_infected_syslogs . '`(`source`, `severity`, `message`, `metadata`, `date`, `userId`)
                      VALUES (\'' . $database->real_escape_string($source) . '\',
                              \'' . $database->real_escape_string($severity) . '\',
                              \'' . $database->real_escape_string($message) . '\',
                              \'' . $database->real_escape_string(json_encode($metadata)) . '\',
                              \'' . date('Y-m-d H:i:s') . '\',
                              \'' . $database->real_escape_string($user != null ? $user->getId() : 0) . '\');');
    //For discord logging
    $techDatabase = Database::getConnection(Settings::db_name_infected_tech);

    $techDatabase->query('INSERT INTO `' . Settings::db_table_infected_tech_discordMessageQueue . '` (`entryId`, `read`) VALUES (' . $database->insert_id . ', 0)');
  }

  public static function getSeverityString(int $severity): ?string {
  	switch ($severity) {
    	case self::SEVERITY_INFO:
    	    return "Info";

    	case self::SEVERITY_ISSUE:
    	    return "Issue";

    	case self::SEVERITY_WARNING:
    	    return "Warning";

    	case self::SEVERITY_CRITICAL:
    	    return "Critical";
  	}
  }
}
?>
