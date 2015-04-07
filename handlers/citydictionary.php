<?php
/**
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
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'database.php';

class CityDictionary {
    /* 
     * Returns the city from given postalcode.
     */
    public static function getCity($postalCode) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `city` FROM `' . Settings::db_table_infected_postalcodes . '`
                                    WHERE `code` = \'' . $database->real_escape_string($postalCode) . '\';');
        
        $database->close();

        $row = $result->fetch_array();
        
        if ($row) {
            return ucfirst(strtolower($row['city']));
        }
    }
    
    /*
     * Returns the postalcode for given city.
     */
    public static function getPostalCode($city) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `code` FROM `' . Settings::db_table_infected_postalcodes . '` 
                                    WHERE `city` = \'' . $database->real_escape_string($city) . '\';');
        
        $database->close();

        $row = $result->fetch_array();
        
        if ($row) {
            return $row['code'];
        }
    }
    
    /*
     * Return true if the specified postal code exists.
     */
    public static function hasPostalCode($postalCode) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_postalcodes . '` 
                                    WHERE `code` = \'' . $database->real_escape_string($postalCode) . '\';');
        
        $database->close();

        return $result->num_rows > 0;
    }
}
?>