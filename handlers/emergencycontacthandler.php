<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/emergencycontact.php';

class EmergencyContactHandler {
    /*
     * Returns the emergency contact with the given id.
     */
    public static function getEmergencyContact($id) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `'. Settings::db_table_infected_emergencycontacts . '`
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();

        if ($row) {
            return new EmergencyContact($row['id'],
                                        $row['userId'],
                                        $row['phone']);
        }
    }
    
    /*
     * Get the emergency contact for the given user.
     */
    public static function getEmergencyContactForUser($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `'. Settings::db_table_infected_emergencycontacts . '`
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();
        
        if ($row) {
            return self::getEmergencyContact($row['id']);
        }
    }
    
    /*
     * Returns a list of all emergency contacts.
     */
    public static function getEmergencyContacts() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_emergencycontacts . '`;');
        
        $mysql->close();

        $emergencyContactsList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($emergencyContactsList, self::getEmergencyContact($row['id']));
        }

        return $emergencyContactsList;
    }
    
    /*
     * Returns true if the specified user has an emergency contact.
     */
    public static function hasEmergencyContact($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `'. Settings::db_table_infected_emergencycontacts . '`
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();

        $row = $result->fetch_array();

        return $row ? true : false;
    }
    
    /*
     * Create a new emergency contact.
     */
    public static function createEmergencyContact($user, $phone) {
        if (!self::hasEmergencyContact($user)) {
                $mysql = MySQL::open(Settings::db_name_infected);

                $mysql->query('INSERT INTO `' . Settings::db_table_infected_emergencycontacts . '` (`userId`, `phone`) 
                               VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\', 
                                       \'' . $mysql->real_escape_string($phone) . '\');');
                $mysql->close();
        } else {
            if (!empty($phone) && $phone != 0) {
                self::updateEmergencyContact($user, $phone);
            } else {
                self::removeEmergencyContact($user);
            }
        }
    }
    
    /* 
     * Update information about a emergency contact.
     */
    public static function updateEmergencyContact($user, $phone) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_emergencycontacts . '` 
                       SET `phone` = \'' . $mysql->real_escape_string($phone) . '\'
                       WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
    }
    
    /*
     * Remove a emergency contact.
     */
    public static function removeEmergencyContact($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_emergencycontacts . '` 
                       WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\';');
        
        $mysql->close();
    }
}
?>