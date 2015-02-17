<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/user.php';

class PasswordResetCodeHandler {
    public static function createPasswordResetCode(User $user) {
        $code = bin2hex(openssl_random_pseudo_bytes(16));
        
        $database = Database::open(Settings::db_name_infected);
        
        if (!self::hasPasswordResetCode($user)) {
            $database->query('INSERT INTO `' . Settings::db_table_infected_passwordresetcodes . '` (`userId`, `code`) 
                              VALUES (\'' . $user->getId() . '\', 
                                      \'' . $database->real_escape_string($code) . '\');');
        } else {
            $database->query('UPDATE `' . Settings::db_table_infected_passwordresetcodes . '` 
                              SET `code` = \'' . $database->real_escape_string($code) . '\'
                              WHERE `userId` = \'' . $user->getId() . '\';');
        }
        
        $database->close();
        
        return $code;
    }
    
    public static function hasPasswordResetCode(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\';');
                              
        $database->close();

        return $result->num_rows > 0;
    }
    
    public static function existsPasswordResetCode($code) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                                    WHERE `code` = \'' . $database->real_escape_string($code) . '\';');
                      
        $database->close();

        return $result->num_rows > 0;
    }
    
    public static function getUserFromPasswordResetCode($code) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
                                    WHERE `id` = (SELECT `userId` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                                                  WHERE `code` = \'' . $database->real_escape_string($code) . '\');');
        
        $database->close();

        return $result->fetch_object('User');
    }
    
    public static function removePasswordResetCode($code) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                          WHERE `code` = \'' . $database->real_escape_string($code) . '\';');
        
        $database->close();
    }
    
    public static function removeUserPasswordResetCode(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                          WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();
    }
}
?>