<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/seatmap.php';

class SeatmapHandler {
    public static function getSeatmap($id) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seatmaps . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
		
		return $result->fetch_object('Seatmap');
    }
    
    public static function getSeatmaps() {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seatmaps . '`;');

        $database->close();

        $seatmapList = array();

        while ($object = $result->fetch_object('Seatmap')) {
            array_push($seatmapList, $object);
        }

        return $seatmapList;
    }
    
    public static function getRows(Seatmap $seatmap) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_rows . '` 
                                    WHERE `seatmapId` = \'' . $seatmap->getId() . '\';');

        $database->close();

        $rowList = array();

        while ($object = $result->fetch_object('Row')) {
            array_push($rowList, $object);
        }

        return $rowList;
    }

    public static function getEvent(Seatmap $seatmap) {
        $database = Database::open(Settings::db_name_infected);

        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_events . '` 
                                    WHERE `seatmapId` = \'' . $seatmap->getId() . '\';');

        $database->close();

        $row = $result->fetch_array();

        if ($row) {
            return EventHandler::getEvent($row['id']);
        }
    }
    
    public static function setBackground(Seatmap $seatmap, $filename) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $database->query('UPDATE `' . Settings::db_table_infected_tickets_seatmaps . '` 
                          SET `backgroundImage` = \'' . $database->real_escape_string($filename) . '\' 
                          WHERE `id` = \'' . $seatmap->getId() . '\';');
    
        $database->close();
    }

    public static function createNewSeatmap($name, $backgroundImage) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $database->query('INSERT INTO ' . Settings::db_table_infected_tickets_seatmaps . '(`humanName`, `backgroundImage`) 
                          VALUES (\'' . $database->real_escape_string($name) . '\', 
                                  \'' . $database->real_escape_string($backgroundImage) . '\')');

        $result = $database->query('SELECT * FROM `' .  Settings::db_table_infected_tickets_seatmaps . '`
                                 WHERE `id` = \'' . $database->insert_id . '\';');

        $database->close();

        return $result->fetch_object('Seatmap');

    }

    public static function duplicateSeatmap(Seatmap $seatmap) {
        $database = Database::open(Settings::db_name_infected_tickets);

        // Create the seatmap object.
        $database->query('INSERT INTO `' . Settings::db_table_infected_tickets_seatmaps . '` (`humanName`, `backgroundImage`) 
                          VALUES (\'Duplicate of ' . $seatmap->getHumanName() . '\', 
                                  \'' . $seatmap->getBackgroundImage() . '\')');
        
        $database->close();
    }
}
?>