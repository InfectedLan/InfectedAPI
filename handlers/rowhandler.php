<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/entrancehandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'objects/row.php';

class RowHandler {
    public static function getRow($id) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_rows . '`
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
		
		return $result->fetch_object('Row');
    }

    public static function getSeats(Row $row) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                    WHERE `rowId` = \'' . $row->getId() . '\';');

        $database->close();

        $seatList = array();

        while ($object = $result->fetch_object('Seat')) {
            array_push($seatList, $object);
        }

        return $seatList;
    }

    public static function createNewRow($seatmap, $x, $y) {
        $database = Database::open(Settings::db_name_infected_tickets);

        //Find out what row is max row
        $highestRowNum = $database->query('SELECT `row` FROM `' . Settings::db_table_infected_tickets_rows . '`
                                           WHERE `seatmapId`=' . $seatmap->getId() . ' 
                                           ORDER BY `row` DESC 
                                           LIMIT 1;');

        $row = mysqli_fetch_array($highestRowNum);

        $newRowNumber = $row['row'] + 1;
        $entrance = EntranceHandler::getEntrance(1); // TODO: Set this somewere else?

        $database->query('INSERT INTO ' . Settings::db_table_infected_tickets_rows . '(`number`, `x`, `y`, `entranceId`, `seatmapId`) 
                          VALUES (\'' . $database->real_escape_string($newRowNumber) . '\', 
                                  \'' . $database->real_escape_string($x) . '\', 
                                  \'' . $database->real_escape_string($y) . '\', 
                                  \'' . $entrance->getId() . '\', 
                                  \'' . $seatmap->getId() . '\');');

        $result = $database->query('SELECT * FROM `' .  Settings::db_table_infected_tickets_rows . '`
                                    WHERE `id` = \'' . $database->insert_id . '\';');

        $database->close();

        return $result->fetch_object('Row');
    }
    
    public static function safeToDelete(Row $row) {
        $seatList = self::getSeats($row);
        
        foreach($seatList as $seat) {
            if (SeatHandler::hasOwner($seat)) {
                return false;
            }
        }
        
        return true;
    }
    
    public static function deleteRow(Row $row) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('DELETE FROM `' . Settings::db_table_infected_tickets_rows . '` 
                                    WHERE `id` = ' . $row->getId() . ';');

        $database->close();

        foreach (self::getSeats($row) as $seat) {
            SeatHandler::deleteSeat($seat);
        }
    }
    
    public static function addSeat(Row $row) {
        $database = Database::open(Settings::db_name_infected_tickets);

        // Find out what seat number we are at.
        $highestSeatNum = $database->query('SELECT `number` FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                            WHERE `rowId` = ' . $row->getId() . ' 
                                            ORDER BY `number` DESC
                                            LIMIT 1;');

        $seatRow = mysqli_fetch_array($highestSeatNum);

        $newSeatNumber = $seatRow['number'] + 1;

        $database->query('INSERT INTO `seats` (`rowId`, `number`) 
                          VALUES (\'' . $row->getId() . '\', 
                                  \'' . $database->real_escape_string($newSeatNumber) . '\');');

        $database->close();
    }
    
    public static function moveRow(Row $row, $x, $y) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $database->query('UPDATE `rows` 
                          SET `x` = \'' . $database->real_escape_string($x) . '\',
                              `y` = \'' . $database->real_escape_string($y) . '\'
                          WHERE `id` = \'' . $row->getId() . '\';');

        $database->close();
    }

    public static function getEvent($row) {
        return SeatmapHandler::getEvent($row->getSeatmap());
    }
}
?>