<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/user.php';

class PasswordResetCodeHandler {
    public static function createPasswordResetCode(User $user) {
        $code = bin2hex(openssl_random_pseudo_bytes(16));
        
        $mysql = MySQL::open(Settings::db_name_infected);
        
        if (!self::hasPasswordResetCode($user)) {
            $mysql->query('INSERT INTO `' . Settings::db_table_infected_passwordresetcodes . '` (`userId`, `code`) 
                           VALUES (\'' . $user->getId() . '\', 
                                   \'' . $mysql->real_escape_string($code) . '\');');
        } else {
            $mysql->query('UPDATE `' . Settings::db_table_infected_passwordresetcodes . '` 
                           SET `code` = \'' . $mysql->real_escape_string($code) . '\'
                           WHERE `userId` = \'' . $user->getId() . '\';');
        }
        
        $mysql->close();
        
        return $code;
    }
    
    public static function hasPasswordResetCode(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                                 WHERE `userId` = \'' . $user->getId() . '\';');
                            
        $mysql->close();

        return $result->num_rows > 0;
    }
    
    public static function existsPasswordResetCode($code) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                                 WHERE `code` = \'' . $mysql->real_escape_string($code) . '\';');
                      
        $mysql->close();

        return $result->num_rows > 0;
    }
    
    public static function getUserFromPasswordResetCode($code) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
                                 WHERE `id` = (SELECT `userId` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                                               WHERE `code` = \'' . $mysql->real_escape_string($code) . '\');');
        
        $mysql->close();

        return $result->fetch_object('User');
    }
    
    public static function removePasswordResetCode($code) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                       WHERE `code` = \'' . $mysql->real_escape_string($code) . '\';');
        
        $mysql->close();
    }
    
    public static function removeUserPasswordResetCode(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                       WHERE `userId` = \'' . $user->getId() . '\';');
        
        $mysql->close();
    }
}
?>