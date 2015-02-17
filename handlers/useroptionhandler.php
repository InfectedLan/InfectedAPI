<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/user.php';

class UserOptionHandler {
    public static function isPhoneHidden(User $user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
								 AND `hidePhone` = \'1\';');
         
        $mysql->close();
         
        return $result->num_rows > 0;
    }
}
?>