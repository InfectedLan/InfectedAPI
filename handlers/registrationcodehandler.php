<?php
require_once 'settings.php';
require_once 'mysql.php';

class RegistrationCodeHandler {
    /* 
     * Get the registration code for a given user, if one exists.
     */
    public static function getRegistrationCode($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `code` FROM `' . Settings::db_table_infected_registrationcodes . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
                            
        $row = $result->fetch_array();
        
        $mysql->close();

        if ($row) {
            return $row['code'];
        }
    }
    
    public static function hasRegistrationCode($code) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_registrationcodes . '` 
                                 WHERE `code` = \'' . $mysql->real_escape_string($code) . '\';');
                            
        $row = $result->fetch_array();
        
        $mysql->close();

        return $row ? true : false;
    }
    
    public static function hasUserRegistrationCode($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_registrationcodes . '` 
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
                            
        $row = $result->fetch_array();
        
        $mysql->close();

        return $row ? true : false;
    }
    
    /*
     * Create a registration code for given user.
     */
    public static function createRegistrationCode($user) {
        $code = bin2hex(openssl_random_pseudo_bytes(16));
        
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_registrationcodes . '` (`userId`, `code`) 
                            VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                    \'' . $code . '\');');
                                    
        $mysql->close();
        
        return $code;
    }
    
    /*
     * Remove registration code for current user, if one exists.
     */
    public static function removeRegistrationCode($code) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_registrationcodes . '` 
                            WHERE `code` = \'' . $mysql->real_escape_string($code) . '\';');
        
        $mysql->close();
    }
    
    /*
     * Remove registration code for current user, if one exists.
     */
    public static function removeUserRegistrationCode($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_registrationcodes . '` 
                            WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
    }
}
?>
