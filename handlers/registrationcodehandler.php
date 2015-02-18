<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/user.php';

class RegistrationCodeHandler {
    /* 
     * Get the registration code by the internal id.
     */
    public static function getRegistrationCode(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `code` FROM `' . Settings::db_table_infected_registrationcodes . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\';');
                            
        $row = $result->fetch_array();
        
        $database->close();

        if ($row) {
            return $row['code'];
        }
    }
    
    /*
     * Returns a list of all registration codes.
     */
    public static function getRegistrationCodes() {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `code` FROM `' . Settings::db_table_infected_registrationcodes . '`;');
        
        $database->close();

        $codeList = array();

        while ($row = $result->fetch_array()) {
            array_push($codeList, $row['code']);
        }

        return $codeList;
    }

    /*
     * Returns true if we got the specified code.
     */
    public static function hasRegistrationCode($code) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_registrationcodes . '` 
                                    WHERE `code` = \'' . $database->real_escape_string($code) . '\';');
        
        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Returns true if we got a registration code for the specified user.
     */
    public static function hasRegistrationCodeByUser(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_registrationcodes . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Create a registration code for the specified user.
     */
    public static function createRegistrationCode(User $user) {
        $code = bin2hex(openssl_random_pseudo_bytes(16));
        
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_registrationcodes . '` (`userId`, `code`) 
                          VALUES (\'' . $user->getId() . '\', 
                                  \'' . $code . '\');');
                                    
        $database->close();
        
        return $code;
    }
    
    /*
     * Remove the specified registration code.
     */
    public static function removeRegistrationCode($code) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_registrationcodes . '` 
                          WHERE `code` = \'' . $database->real_escape_string($code) . '\';');
        
        $database->close();
    }
    
    /*
     * Remove registration code for specified user.
     */
    public static function removeRegistrationCodeByUser(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_registrationcodes . '` 
                          WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();
    }
}
?>