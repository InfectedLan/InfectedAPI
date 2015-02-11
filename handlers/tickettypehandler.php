<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/tickettype.php';

class TicketTypeHandler {
	/*
	 * Get a ticket type by the internal id.
	 */
    public static function getTicketType($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickettypes . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('TicketType');
    }

    /* 
     * Get a list of all ticket types.
     */
    public static function getTicketTypes() {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickettypes . '`;');
        
        $mysql->close();
        
        $ticketTypeList = array();
        
        while ($object = $result->fetch_object('TicketType')) {
            array_push($ticketTypeList, $object);
        }

        return $ticketTypeList;
    }
}
?>