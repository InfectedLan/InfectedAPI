<?php
require_once 'settings.php';
require_once 'mysql.php';

class UserOptionHandler {
    public static function isPhoneHidden($user) {
        $mysql = MySQL::open(Settings::db_name_infected);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_useroptions . '` 
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
								 AND `hidePhone` = \'1\';');
         
        $mysql->close();
         
        $row = $result->fetch_array();

        return $row ? true : false;
    }
}
?>