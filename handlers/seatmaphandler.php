<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/rowhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/seatmap.php';

class SeatmapHandler {
    public static function getSeatmap($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seatmaps . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('Seatmap');
    }
    
    public static function getSeatmaps() {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_seatmaps . '`;');

        $mysql->close();

        $seatmapList = array();

        while ($object = $result->fetch_object('Seatmap')) {
            array_push($seatmapList, $object);
        }

        return $seatmapList;
    }
    
    public static function getRows($seatmap) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_rows . '` 
                                 WHERE `seatmap` = \'' . $mysql->real_escape_string($seatmap->getId()) . '\';');

        $mysql->close();

        $rowList = array();

        while ($row = $result->fetch_array()) {
            array_push($rowList, RowHandler::getRow($row['id']));
        }

        return $rowList;
    }

    public static function getEvent($seatmap) {
        $mysql = MySQL::open(Settings::db_name_infected);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_events . '` 
                                 WHERE `seatmap`=' . $mysql->real_escape_string($seatmap->getId()) . ';');

        $mysql->close();

        $row = $result->fetch_array();

        if ($row) {
            return EventHandler::getEvent($row['id']);
        }
    }
    
    public static function setBackground($seatmap, $filename) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $mysql->query('UPDATE `' . Settings::db_table_infected_tickets_seatmaps . '` 
                       SET `backgroundImage` = \'' . $mysql->real_escape_string($filename) . '\' 
                       WHERE `id` = \'' . $mysql->real_escape_string($seatmap->getId()) . '\';');
    
        $mysql->close();
    }

    public static function createNewSeatmap($name, $backgroundImage) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $mysql->query('INSERT INTO ' . Settings::db_table_infected_tickets_seatmaps . '(`humanName`, `backgroundImage`) 
                       VALUES (\'' . $mysql->real_escape_string($name) . '\', 
                               \'' . $mysql->real_escape_string($backgroundImage) . '\')');

        $result = $mysql->query('SELECT * FROM `' .  Settings::db_table_infected_tickets_seatmaps . '`
                                 ORDER BY id DESC
                                 LIMIT 1;');

        $mysql->close();

        return $result->fetch_object('Seatmap');

    }

    public static function duplicateSeatmap($seatmap) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        //Create the seatmap object
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_seatmaps . '` (`humanName`, `backgroundImage`) 
                       VALUES (\'Duplicate of ' . $mysql->real_escape_string($seatmap->getHumanName()) . '\', 
                               \'' . $mysql->real_escape_string($seatmap->getBackgroundImage()) . '\')');

        $mysql->close();
    }
}
?>