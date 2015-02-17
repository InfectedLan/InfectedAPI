<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/emergencycontact.php';
require_once 'objects/user.php';

class EmergencyContactHandler {
    /*
     * Get an emergenctcontacts by the internal id.
     */
    public static function getEmergencyContact($id) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `'. Settings::db_table_infected_emergencycontacts . '`
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('EmergencyContact');
    }
    
    /*
     * Get the emergency contact for the given user.
     */
    public static function getEmergencyContactForUser(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `'. Settings::db_table_infected_emergencycontacts . '`
                                 WHERE `userId` = \'' . $user->getId() . '\';');
        
        $mysql->close();

        return $result->fetch_object('EmergencyContact');
    }
    
    /*
     * Returns a list of all emergency contacts.
     */
    public static function getEmergencyContacts() {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_emergencycontacts . '`;');
        
        $mysql->close();

        $emergencyContactsList = array();

        while ($object = $result->fetch_object('EmergencyContact')) {
            array_push($emergenctContactList, $object);
        }

        return $emergencyContactsList;
    }
    
    /*
     * Returns true if the specified user has an emergency contact.
     */
    public static function hasEmergencyContact(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `'. Settings::db_table_infected_emergencycontacts . '`
                                 WHERE `userId` = \'' . $user->getId() . '\';');
        
        $mysql->close();

        return $result->num_rows > 0;
    }
    
    /*
     * Create a new emergency contact.
     */
    public static function createEmergencyContact(User $user, $phone) {
        if (!self::hasEmergencyContact($user)) {
                $mysql = MySQL::open(Settings::db_name_infected);

                $mysql->query('INSERT INTO `' . Settings::db_table_infected_emergencycontacts . '` (`userId`, `phone`) 
                               VALUES (\'' . $user->getId() . '\', 
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
    public static function updateEmergencyContact(User $user, $phone) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_emergencycontacts . '` 
                       SET `phone` = \'' . $mysql->real_escape_string($phone) . '\'
                       WHERE `userId` = \'' . $user->getId() . '\';');
        
        $mysql->close();
    }
    
    /*
     * Remove a emergency contact.
     */
    public static function removeEmergencyContact(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_emergencycontacts . '` 
                       WHERE `userId` = \'' . $user->getId() . '\';');
        
        $mysql->close();
    }
}
?>