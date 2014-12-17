<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/row.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/seathandler.php';
    
class RowHandler {
    public static function getRow($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_rows . '`
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                      
        $row = $result->fetch_array();

        $mysql->close();

        if ($row) {
            return new Row($row['id'], 
                           $row['number'],
                           $row['x'], 
                           $row['y'], 
                           $row['entrance'], 
                           $row['seatmap']);
        }
    }

    public static function getSeats($row) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                      WHERE `rowId` = \'' . $mysql->real_escape_string($row->getId()) . '\';');

        $seatArray = array();

        while ($seat = $result->fetch_array()) {
            array_push($seatArray, SeatHandler::getSeat($seat['id']));
        }

        $mysql->close();

        return $seatArray;
    }

    public static function createNewRow($seatmapId, $x, $y) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        //Find out what row is max row
        $highestRowNum = $mysql->query('SELECT `row` FROM `' . Settings::db_table_infected_tickets_rows . '`
                                             WHERE `seatmap`=' . $mysql->real_escape_string($seatmapId) . ' 
                                             ORDER BY `row` DESC LIMIT 1;');

        $row = mysqli_fetch_array($highestRowNum);

        $newRowNumber = $row['row'] + 1;
        $entrance = 1; // TODO: Set this somewere else?

        $mysql->query('INSERT INTO ' . Settings::db_table_infected_tickets_rows . '(`number`, `x`, `y`, `entrance`, `seatmap`) 
                            VALUES (\'' . $mysql->real_escape_string($newRowNumber) . '\', 
                                      ' . $mysql->real_escape_string($x) . ', 
                                      ' . $mysql->real_escape_string($y) . ', 
                                      ' . $mysql->real_escape_string($entrance) . ', 
                                      ' . $mysql->real_escape_string($seatmapId) . ');');

        $result = $mysql->query('SELECT `id` FROM `' .  Settings::db_table_infected_tickets_rows . '`
                                      ORDER BY `id` DESC 
                                      LIMIT 1;');

        $row = $result->fetch_array();

        $mysql->close();

        if ($row) {
            return self::getRow($row['id']);
        }

    }
    
    public static function safeToDelete($row) {
        $seats = self::getSeats($row);
        
        foreach($seats as $seat) {
            if (SeatHandler::hasOwner($seat)) {
                return false;
            }
        }
        
        return true;
    }
    
    public static function deleteRow($row) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('DELETE FROM `' . Settings::db_table_infected_tickets_rows . '` 
                                      WHERE `id` = ' . $mysql->real_escape_string($row->getId()) . ';');

        $seats = self::getSeats($row);
        
        foreach($seats as $seat) {
            SeatHandler::deleteSeat($seat);
        }

        $mysql->close();
    }
    
    public static function addSeat($row) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        //Find out what seat number we are at
        $highestSeatNum = $mysql->query('SELECT `number` FROM `' . Settings::db_table_infected_tickets_seats . '` 
                                              WHERE `rowId` = ' . $mysql->real_escape_string($row->getId()) . ' 
                                              ORDER BY `number` DESC LIMIT 1;');

        $seatRow = mysqli_fetch_array($highestSeatNum);

        $newSeatNumber = $seatRow['number']+1;

        $mysql->query('INSERT INTO `seats` (`rowId`, `number`) 
                            VALUES (' . $mysql->real_escape_string($row->getId()) . ', 
                                    ' . $mysql->real_escape_string($newSeatNumber) . ')');

        $mysql->close();
    }
    
    public static function moveRow($row, $x, $y) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $mysql->query('UPDATE `rows` SET `x`=' . $x . ',`y`=' . $y . ' 
                            WHERE `id` = ' . $mysql->real_escape_string($row->getId()) . ';');

        $mysql->close();
    }

    public static function getEvent($row) {
        return SeatmapHandler::getEvent(SeatmapHandler::getSeatmap($row->getSeatmap()));
    }
}
?>