<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/readyhandler.php';

class ReadyHandlerHandler {
    public static function getReadyHandler($id) {
        $database = Database::open(Settings::db_name_infected_compo);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_readyInstances . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
		
		return $result->fetch_object('ReadyHandler');
    }
}
?>