<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/rowhandler.php';
require_once 'objects/seat.php';

class SeatHandler {
    public static function getSeat($id) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
         
        $database->close();
		
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
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('DELETE FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                    WHERE `id` = ' . $seat->getId() . ';');

        $database->close();
    }

    public static function hasOwner(Seat $seat) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                    WHERE `seatId` = ' . $seat->getId() . ';');

        $row = $result->fetch_array();

        return $result->num_rows > 0;
    }

    public static function getOwner(Seat $seat) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT `userId` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                    WHERE `seatId` = ' . $seat->getId() . ';');
        
        $database->close();

        $row = $result->fetch_array();

        if ($row) {
            return UserHandler::getUser($row['userId']);
        }
    }

    public static function getTicket(Seat $seat) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
                                    WHERE `seatId` = ' . $seat->getId() . ';');
        
        $database->close();

        return $result->fetch_object('Ticket');
    }

    public static function getEvent(Seat $seat) {
        return RowHandler::getEvent($seat->getRow());
    }
}
?>