<?php
require_once 'settings.php';
require_once 'mysql.php';

class CityDictionary {
    /* 
     * Returns the city from given postalcode.
     */
    public static function getCity($postalCode) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `city` FROM `' . Settings::db_table_infected_postalcodes . '`
                                 WHERE `code` = \'' . $mysql->real_escape_string($postalCode) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();
        
        if ($row) {
            return ucfirst(strtolower($row['city']));
        }
    }
    
    /*
     * Returns the postalcode for given city.
     */
    public static function getPostalCode($city) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `code` FROM `' . Settings::db_table_infected_postalcodes . '` 
                                 WHERE `city` = \'' . $mysql->real_escape_string($city) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();
        
        if ($row) {
            return $row['city'];
        }
    }
    
    /*
     * Return true if the specified postal code exists.
     */
    public static function hasPostalCode($postalCode) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_postalcodes . '` 
                                 WHERE `code` = \'' . $mysql->real_escape_string($postalCode) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();
        
        return $row ? true : false;
    }
}
?>
