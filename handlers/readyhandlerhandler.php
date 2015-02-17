<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/readyhandler.php';

class ReadyHandlerHandler {
    public static function getReadyHandler($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_readyInstances . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('ReadyHandler');
    }
}
?>