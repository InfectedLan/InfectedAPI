<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/seat.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/rowhandler.php';

class SeatHandler {
    public static function getSeat($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('Seat');
    }

    /*
     * Returns a string representation of the seat
     */
    public static function getHumanString($seat) {
        $row = $seat->getRow();
        
        return 'R' . $row->getNumber() . ' S' . $seat->getNumber();
    }

    public static function deleteSeat($seat) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('DELETE FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                 WHERE `id` = ' . $mysql->real_escape_string($seat->getId()) . ';');

        $mysql->close();
    }

    public static function hasOwner($seat) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `seatId` = ' . $mysql->real_escape_string($seat->getId()) . ';');

        $row = $result->fetch_array();

        $mysql->close();

        return $row ? true : false;
    }

    public static function getOwner($seat) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `userId` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `seatId` = ' . $mysql->real_escape_string($seat->getId()) . ';');
        
        $mysql->close();

        $row = $result->fetch_array();

        if ($row) {
            return UserHandler::getUser($row['userId']);
        }        
    }

    public static function getTicket($seat) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `seatId` = ' . $mysql->real_escape_string($seat->getId()) . ';');
        
        $mysql->close();

        $row = $result->fetch_array();

        if ($row) {
            return TicketHandler::getTicket($row['id']);
        }        
    }

    public static function getEvent($seat) {
        return RowHandler::getEvent($seat->getRow());
    }
}
?>