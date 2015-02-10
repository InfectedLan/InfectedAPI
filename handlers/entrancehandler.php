<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/entrance.php';

class EntranceHandler {
    /*
     * Get an entrance by the internal id.
     */
    public static function getEntrance($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_entrances . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                    
        $mysql->close();

        $row = $result->fetch_array();

        if ($row) {
            return new Entrance($row['id'],
                                $row['name'], 
                                $row['title']);
        }
    }
    
    /*
     * Get an entrance by name.
     */
    public static function getEntranceByName($name) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_entrances . '` 
                                 WHERE `name` = \'' . $mysql->real_escape_string($name) . '\';');
                                    
        $mysql->close();

        $row = $result->fetch_array();

        if ($row) {
            return self::getEntrance($row['id']);
        }
    }

    /*
     * Get a list of all entrances.
     */
    public static function getEntrances() {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_entrance . '`;');

        $mysql->close();

        $entranceList = array();

        while($row = $result->fetch_array()) {
            array_push($entranceList, self::getEntrance($row['id']));
        }

        return $entranceList;
    }
}
?>