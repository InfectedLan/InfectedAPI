<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/user.php';

class UserOptionHandler {
	/*
	 * Returns true is the phone number is set to hidden for the specified user.
	 */
    public static function isPhoneHidden(User $user) {
        $database = Database::open(Settings::db_name_infected);
        
        $result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '` 
                                    WHERE `userId` = \'' . $user->getId() . '\'
								    AND `hidePhone` = \'1\';');
         
        $database->close();
         
        return $result->num_rows > 0;
    }
}
?>