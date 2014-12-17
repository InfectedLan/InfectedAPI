<?php
require_once 'settings.php';
require_once 'mysql.php';

class CityDictionary {
    /* 
     * Returns the city from given postalcode.
     */
    public static function getCity($postalcode) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `city` FROM `' . Settings::db_table_infected_postalcodes . '`
                                      WHERE `code` = \'' . $mysql->real_escape_string($postalcode) . '\';');
                                      
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
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
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        if ($row) {
            return $row['city'];
        }
    }
    
    public static function hasPostalCode($code) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_postalcodes . '` 
                                      WHERE `code` = \'' . $mysql->real_escape_string($code) . '\';');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        return $row ? true : false;
    }
}
?>
