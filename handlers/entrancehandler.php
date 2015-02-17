<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/entrance.php';

class EntranceHandler {
    /*
     * Get an entrance by the internal id.
     */
    public static function getEntrance($id) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_entrances . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
                                    
        $database->close();
		
		return $result->fetch_object('Entrance');
    }
    
    /*
     * Get an entrance by name.
     */
    public static function getEntranceByName($name) {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_entrances . '` 
                                    WHERE `name` = \'' . $database->real_escape_string($name) . '\';');
                                    
        $database->close();

        return $result->fetch_object('Entrance');
    }

    /*
     * Get a list of all entrances.
     */
    public static function getEntrances() {
        $database = Database::open(Settings::db_name_infected_tickets);

        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_entrance . '`;');

        $database->close();

        $entranceList = array();

        while ($object = $result->fetch_object('Entrance')) {
            array_push($entranceList, $object);
        }

        return $entranceList;
    }
}
?>