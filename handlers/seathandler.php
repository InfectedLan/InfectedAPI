<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/rowhandler.php';
require_once 'objects/seat.php';

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
    public static function getHumanString(Seat $seat) {
        $row = $seat->getRow();
        
        return 'R' . $row->getNumber() . ' S' . $seat->getNumber();
    }

    public static function deleteSeat(Seat $seat) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('DELETE FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                 WHERE `id` = ' . $mysql->real_escape_string($seat->getId()) . ';');

        $mysql->close();
    }

    public static function hasOwner(Seat $seat) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `seatId` = ' . $mysql->real_escape_string($seat->getId()) . ';');

        $row = $result->fetch_array();

        return $result->num_rows > 0;
    }

    public static function getOwner(Seat $seat) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `userId` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `seatId` = ' . $mysql->real_escape_string($seat->getId()) . ';');
        
        $mysql->close();

        $row = $result->fetch_array();

        if ($row) {
            return UserHandler::getUser($row['userId']);
        }
    }

    public static function getTicket(Seat $seat) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                 WHERE `seatId` = ' . $mysql->real_escape_string($seat->getId()) . ';');
        
        $mysql->close();

        return $result->fetch_object('Ticket');
    }

    public static function getEvent(Seat $seat) {
        return RowHandler::getEvent($seat->getRow());
    }
}
?>