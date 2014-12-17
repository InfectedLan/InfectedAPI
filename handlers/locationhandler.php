<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/location.php';

class LocationHandler {
    // Returns the location with the given id.
    public static function getLocation($id) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `'. Settings::db_table_infected_locations . '`
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');

        $row = $result->fetch_array();
        
        $mysql->close();

        if ($row) {
            return new Location($row['id'],
                                $row['name'],
                                $row['title']);
        }
    }
    
    // Returns a list of all locations.
    public static function getLocations() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_locations . '`;');
        
        $locationList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($locationList, self::getLocation($row['id']));
        }
        
        $mysql->close();

        return $locationList;
    }
}
?>
