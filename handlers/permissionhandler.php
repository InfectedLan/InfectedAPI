/*
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/permission.php';

class PermissionHandler {
    /*
     * Get the permission by the internal id.
     */
    public static function getPermission($id) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
        
		return $result->fetch_object('Permission');
    }

    /*
     * Returns the permission with the given value.
     */
    public static function getPermissionByValue($value) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
                                    WHERE `value` = \'' . $database->real_escape_string($value) . '\';');
        
        $database->close();
        
        return $result->fetch_object('Permission');
    }
    
    /*
     * Returns a list of all permissions.
     */
    public static function getPermissions() {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_permissions . '`
								    ORDER BY `value` ASC;');
        
        $database->close();

        $permissionList = array();
        
        while ($object = $result->fetch_object('Permission')) {
            array_push($permissionList, $object);
        }

        return $permissionList;
    }
}
?>