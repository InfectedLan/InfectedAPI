<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/entrance.php';

class EntranceHandler {
    public static function getEntrance($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_entrances . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                    
        $row = $result->fetch_array();

        $mysql->close();

        if ($row) {
            return new Entrance($row['id'],
                                $row['name'], 
                                $row['title']);
        }
    }
    
    public static function getEntrances() {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_entrance . '`;');

        $entranceList = array();

        while($row = $result->fetch_array()) {
            array_push($entranceList, self::getEntrance($row['id']));
        }

        $mysql->close();

        return $entranceList;
    }
}
?>