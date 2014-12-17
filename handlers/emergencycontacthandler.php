<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/emergencycontact.php';

class EmergencyContactHandler {
    // Returns the emergency contact with the given id.
    public static function getEmergencyContact($id) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `'. Settings::db_table_infected_emergencycontacts . '`
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();

        if ($row) {
            return new EmergencyContact($row['id'],
                                        $row['userId'],
                                        $row['phone']);
        }
    }
    
    public static function getEmergencyContactForUser($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `'. Settings::db_table_infected_emergencycontacts . '`
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();

        if ($row) {
            return self::getEmergencyContact($row['id']);
        }
    }
    
    // Returns a list of all emergency contacts.
    public static function getEmergencyContacts() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_emergencycontacts . '`;');
        
        $emergencyContactsList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($emergencyContactsList, self::getEmergencyContact($row['id']));
        }
        
        $mysql->close();

        return $emergencyContactsList;
    }
    
    public static function hasEmergencyContact($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `'. Settings::db_table_infected_emergencycontacts . '`
                                      WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();

        return $row ? true : false;
    }
    
    /* Create new emergency contact */
    public static function createEmergencyContact($user, $phone) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        if (!self::hasEmergencyContact($user)) {
                echo 'Hello darfin.';
        
                $mysql->query('INSERT INTO `' . Settings::db_table_infected_emergencycontacts . '` (`userId`, `phone`) 
                                    VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                            \'' . $mysql->real_escape_string($phone) . '\');');
        } else {
            if (!empty($phone) && $phone != 0) {
                self::updateEmergencyContact($user, $phone);
            } else {
                self::removeEmergencyContact($user);
            }
        }
    
        $mysql->close();
    }
    
    /* 
     * Update information about a game.
     */
    public static function updateEmergencyContact($user, $phone) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_emergencycontacts . '` 
                            SET `phone` = \'' . $mysql->real_escape_string($phone) . '\'
                            WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
    }
    
    /* Remove a emergency contact */
    public static function removeEmergencyContact($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_emergencycontacts . '` 
                            WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
    }
}
?>
