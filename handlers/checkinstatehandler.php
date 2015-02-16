<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/checkinstate.php';

class CheckInStateHandler {
    /*
     * Get a checkinstate by the internal id.
     */
    public static function getCheckInState($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_checkinstate . '` 
                                 WHERE `id` = \'' . $id . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('CheckInState');
    }

    /*
     * Check in a ticket.
     */
    public static function checkIn($ticket) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $reuslt = $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_checkinstate . '` (`ticketId`, `userId`) 
                                 VALUES (\'' . $mysql->real_escape_string($ticket->getId()) . '\',
                                         \'' . $mysql->real_escape_string($ticket->getUser()->getId()) . '\');');

        $mysql->close();
    }

    /*
     * Returns true if the specified ticket is checked in.
     */
    public static function isCheckedIn($ticket) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_checkinstate . '` 
                                 WHERE `ticketId` = \'' . $mysql->real_escape_string($ticket->getId()) . '\';');

        $mysql->close();

        $row = $result->fetch_array();

        return $row ? true : false;
    }
}
?>