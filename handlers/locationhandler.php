<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/location.php';

class LocationHandler {
    /*
     * Returns the location with the given id.
     */
    public static function getLocation($id) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `'. Settings::db_table_infected_locations . '`
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
		
        $database->close();
		
		return $result->fetch_object('Location');
    }
    
    /* 
     * Returns a list of all locations.
     */
    public static function getLocations() {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_locations . '`;');
        
        $database->close();

        $locationList = array();
        
        while ($object = $result->fetch_object('Location')) {
            array_push($locationList, $object);
        }

        return $locationList;
    }
}
?>
