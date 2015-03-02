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
require_once 'objects/tickettype.php';

class TicketTypeHandler {
	/*
	 * Get a ticket type by the internal id.
	 */
    public static function getTicketType($id) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickettypes . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
		
		return $result->fetch_object('TicketType');
    }

    /* 
     * Get a list of all ticket types.
     */
    public static function getTicketTypes() {
        $database = Database::open(Settings::db_name_infected_tickets);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickettypes . '`;');
        
        $database->close();
        
        $ticketTypeList = array();
        
        while ($object = $result->fetch_object('TicketType')) {
            array_push($ticketTypeList, $object);
        }

        return $ticketTypeList;
    }
}
?>