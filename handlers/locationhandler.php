<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/location.php';

class LocationHandler {
    /*
     * Returns the location with the given id.
     */
    public static function getLocation($id) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `'. Settings::db_table_infected_locations . '`
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
		
        $mysql->close();
		
		return $result->fetch_object('Location');
    }
    
    /* 
     * Returns a list of all locations.
     */
    public static function getLocations() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_locations . '`;');
        
        $mysql->close();

        $locationList = array();
        
        while ($object = $result->fetch_object('Location')) {
            array_push($locationList, $object);
        }

        return $locationList;
    }
}
?>
