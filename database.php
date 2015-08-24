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
require_once 'secret.php';

class Database {
	/*
	 * Opens a connection to specified database.
	 */
	public static function open($database) {
		// Create connection
		$mysqli = new mysqli(Settings::db_host,
							 Secret::db_username,
							 Secret::db_password,
							 $database);

		// Check connection.
		if ($mysqli->connect_errno) {
			printf('Connect failed: %s\n', $mysqli->connect_error);
			exit();
		}

		// Change character set to utf8.
		if (!$mysqli->set_charset('utf8')) {
			printf('Error loading character set utf8: %s\n', $mysqli->error);
		}

		return $mysqli;
	}
}
?>
