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
require_once 'interfaces/task.php';

class TaskManager {
	/* 
     * Get a task by given id.
     */
    public static function getTask($id) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `'. Settings::db_table_infected_tasks . '`
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

        $database->close();

		$row = $result->fetch_array();

		return unserialize($row['object']);
    }

    /* 
     * Get a list of all the tasks.
     */
    public static function getTasks() {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `'. Settings::db_table_infected_tasks . '`;');
        
        $database->close();
        
        $taskList = array();
        
        while ($row = $result->fetch_row()) {
            array_push($taskList, unserialize($row['object']));
        }
        
        return $taskList;
    }

    /* 
     * Create new task.
     */
    public static function createTask(Task $task) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_tasks . '` (`object`) 
                          VALUES (\'' . serialize($task) . '\');');
        
        $database->close();
    }

    /* 
     * Remove a task.
     */
    public static function removeTask($id) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_tasks . '` 
                          WHERE `id` = \'' . $id . '\';');
        
        $database->close();
    }
}
?>