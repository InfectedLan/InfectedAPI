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
require_once 'interfaces/task.php';

class TaskManager {
	/*
	 * Get a task by given id.
	 */
	public static function getTask(int $id): ?object {
		$database = Database::getConnection(Settings::getValue("db_name_infected"));

		$result = $database->query('SELECT * FROM `'. DatabaseConstants::db_table_infected_tasks . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$row = $result->fetch_array();

		return unserialize($row['object']);
	}

	/*
	 * Get a list of all the tasks.
	 */
	public static function getTasks(): array {
		$database = Database::getConnection(Settings::getValue("db_name_infected"));

		$result = $database->query('SELECT * FROM `'. DatabaseConstants::db_table_infected_tasks . '`;');

		$taskList = [];

		while ($row = $result->fetch_row()) {
			$taskList[] = unserialize($row['object']);
		}

		return $taskList;
	}

	/*
	 * Create new task.
	 */
	public static function createTask(ITask $task) {
		$database = Database::getConnection(Settings::getValue("db_name_infected"));

		$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_tasks . '` (`object`)
						  				VALUES (\'' . serialize($task) . '\');');
	}

	/*
	 * Remove a task.
	 */
	public static function removeTask(int $id) {
		$database = Database::getConnection(Settings::getValue("db_name_infected"));

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_tasks . '`
						  				WHERE `id` = \'' . $id . '\';');
	}
}
?>
