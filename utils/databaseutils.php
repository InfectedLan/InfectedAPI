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
require_once 'objects/event.php';

class DatabaseUtils {
	/*
	 * Copies the contents of the given table to the new one.
	 */
	public static function copyTable($databaseName, $fromTableName, $toTableName) {
		$database = Database::open($databaseName);

		$database->query('INSERT INTO `' . $toTableName . '`
						  				SELECT * FROM `' . $fromTableName . '`;');

		$database->close();
	}

	/*
	 * Copies the contents of the given table to the new one, but only the specified selection and referenceField is changed with the new value.
	 */
	public static function copyTableSelection($databaseName, $tableName, $referenceField, $fromValue, $toValue) {
		$database = Database::open($databaseName);

		$database->query('CREATE TEMPORARY TABLE `temporary`
								  		SELECT * FROM `' . $tableName . '`
								  		WHERE `' . $referenceField . '` = \'' . $fromValue . '\';');

		$database->query('UPDATE `temporary` SET `id` = NULL;');

		$database->query('UPDATE `temporary` SET `' . $referenceField . '` = \'' . $toValue . '\'
								  		WHERE `' . $referenceField . '` = \'' . $fromValue . '\';');

		$database->query('INSERT INTO `' . $tableName . '`
									  	SELECT * FROM `temporary`
						  				AND NOT EXISTS (SELECT `id` FROM `' . $tableName . '`
										  								WHERE `' . $referenceField . '` = \'' . $toValue . '\');');

		$database->query('DROP TABLE `temporary`;');

		$database->close();
	}
}
