<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/readyhandler.php';

class ReadyHandlerHandler {
    public static function getReadyHandler($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_readyInstances . '` 
                                      WHERE `id` = \'' . $id . '\';');
        
        $row = $result->fetch_array();
        
        $mysql->close();
        
        if ($row) {
            return new ReadyHandler($row['id'], 
                                    $row['compoId']);
        }
    }
}
?>