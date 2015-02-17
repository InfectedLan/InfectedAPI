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