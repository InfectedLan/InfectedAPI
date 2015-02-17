<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/user.php';

class RegistrationCodeHandler {
    /* 
     * Get the registration code for a given user, if one exists.
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
    
    public static function hasRegistrationCode($code) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_registrationcodes . '` 
                                    WHERE `code` = \'' . $database->real_escape_string($code) . '\';');
        
        $database->close();

        return $result->num_rows > 0;
    }
    
    public static function hasUserRegistrationCode(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_registrationcodes . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Create a registration code for given user.
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
     * Remove registration code for current user, if one exists.
     */
    public static function removeRegistrationCode($code) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_registrationcodes . '` 
                          WHERE `code` = \'' . $database->real_escape_string($code) . '\';');
        
        $database->close();
    }
    
    /*
     * Remove registration code for current user, if one exists.
     */
    public static function removeUserRegistrationCode(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_registrationcodes . '` 
                          WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();
    }
}
?>