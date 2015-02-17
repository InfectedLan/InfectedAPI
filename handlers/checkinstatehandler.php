<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/checkinstate.php';
require_once 'objects/ticket.php';

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
    public static function checkIn(Ticket $ticket) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $reuslt = $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_checkinstate . '` (`ticketId`, `userId`) 
                                 VALUES (\'' . $ticket->getId() . '\',
                                         \'' . $ticket->getUser()->getId() . '\');');

        $mysql->close();
    }

    /*
     * Returns true if the specified ticket is checked in.
     */
    public static function isCheckedIn(Ticket $ticket) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_checkinstate . '` 
                                 WHERE `ticketId` = \'' . $ticket->getId() . '\';');

        $mysql->close();

        return $result->num_rows > 0;
    }
}
?>