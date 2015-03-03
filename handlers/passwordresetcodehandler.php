<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'database.php';
require_once 'objects/user.php';

class PasswordResetCodeHandler {
    /*
     * Get the password reset code by the internal id.
     */
    public static function getPasswordResetCode($id) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `code` FROM `' . Settings::db_table_infected_passwordresetcodes . '`
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();

        $row = $result->fetch_array();

        if ($row) {
            return $row['code'];
        }
    }

    /*
     * Returns a list of all password reset codes.
     */
    public static function getPasswordResetCodes() {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `code` FROM `' . Settings::db_table_infected_passwordresetcodes . '`;');
        
        $database->close();

        $codeList = array();

        while ($row = $result->fetch_array()) {
            array_push($codeList, $row['code']);
        }

        return $codeList;
    }
    
    /*
     * Returns true if we've got the specified code.
     */
    public static function hasPasswordResetCode($code) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                                    WHERE `code` = \'' . $database->real_escape_string($code) . '\';');
                      
        $database->close();

        return $result->num_rows > 0;
    }

    /*
     * Returns true if we've got a code for the specified user.
     */
    public static function hasPasswordResetCodeByUser(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\';');
                              
        $database->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Create a new password reset code for the specified user.
     */
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

    /*
     * Remove the specified password reset code.
     */
    public static function removePasswordResetCode($code) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                          WHERE `code` = \'' . $database->real_escape_string($code) . '\';');
        
        $database->close();
    }
    
    /*
     * Remove the password reset code for the specified user.
     */
    public static function removePasswordResetCodeByUser(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                          WHERE `userId` = \'' . $user->getId() . '\';');
        
        $database->close();
    }

    /*
     * Returns the user with the specified password reset code.
     */
    public static function getUserFromPasswordResetCode($code) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
                                    WHERE `id` = (SELECT `userId` FROM `' . Settings::db_table_infected_passwordresetcodes . '` 
                                                  WHERE `code` = \'' . $database->real_escape_string($code) . '\');');
        
        $database->close();

        return $result->fetch_object('User');
    }
}
?>