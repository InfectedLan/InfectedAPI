<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/entrancehandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'objects/row.php';

class RowHandler {
    public static function getRow($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_rows . '`
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('Row');
    }

    public static function getSeats(Row $row) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                 WHERE `rowId` = \'' . $row->getId() . '\';');

        $mysql->close();

        $seatList = array();

        while ($object = $result->fetch_object('Seat')) {
            array_push($seatList, $object);
        }

        return $seatList;
    }

    public static function createNewRow($seatmap, $x, $y) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        //Find out what row is max row
        $highestRowNum = $mysql->query('SELECT `row` FROM `' . Settings::db_table_infected_tickets_rows . '`
                                        WHERE `seatmapId`=' . $seatmap->getId() . ' 
                                        ORDER BY `row` DESC 
                                        LIMIT 1;');

        $row = mysqli_fetch_array($highestRowNum);

        $newRowNumber = $row['row'] + 1;
        $entrance = EntranceHandler::getEntrance(1); // TODO: Set this somewere else?

        $mysql->query('INSERT INTO ' . Settings::db_table_infected_tickets_rows . '(`number`, `x`, `y`, `entranceId`, `seatmapId`) 
                       VALUES (\'' . $mysql->real_escape_string($newRowNumber) . '\', 
                               \'' . $mysql->real_escape_string($x) . '\', 
                               \'' . $mysql->real_escape_string($y) . '\', 
                               \'' . $entrance->getId() . '\', 
                               \'' . $seatmap->getId() . '\');');

        $result = $mysql->query('SELECT * FROM `' .  Settings::db_table_infected_tickets_rows . '`
                                 WHERE `id` = \'' . $mysql->insert_id . '\';');

        $mysql->close();

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
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('DELETE FROM `' . Settings::db_table_infected_tickets_rows . '` 
                                 WHERE `id` = ' . $row->getId() . ';');

        $mysql->close();

        foreach (self::getSeats($row) as $seat) {
            SeatHandler::deleteSeat($seat);
        }
    }
    
    public static function addSeat(Row $row) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        // Find out what seat number we are at.
        $highestSeatNum = $mysql->query('SELECT `number` FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                         WHERE `rowId` = ' . $row->getId() . ' 
                                         ORDER BY `number` DESC
                                         LIMIT 1;');

        $seatRow = mysqli_fetch_array($highestSeatNum);

        $newSeatNumber = $seatRow['number'] + 1;

        $mysql->query('INSERT INTO `seats` (`rowId`, `number`) 
                       VALUES (\'' . $row->getId() . '\', 
                               \'' . $mysql->real_escape_string($newSeatNumber) . '\');');

        $mysql->close();
    }
    
    public static function moveRow(Row $row, $x, $y) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $mysql->query('UPDATE `rows` 
                       SET `x` = \'' . $mysql->real_escape_string($x) . '\',
                           `y` = \'' . $mysql->real_escape_string($y) . '\'
                       WHERE `id` = \'' . $row->getId() . '\';');

        $mysql->close();
    }

    public static function getEvent($row) {
        return SeatmapHandler::getEvent($row->getSeatmap());
    }
}
?>