<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/game.php';

class GameHandler {
    /*
     * Get a game by the internal id.
     */
    public static function getGame($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_games . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('Game');
    }
    
    public static function getGames() {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_games . '`;');
                   
        $mysql->close();

        $gameList = array();
        
        while ($object = $result->fetch_object('Game')) {
            array_push($gameList, $object);
        }
        
        return $gameList;
    }
    
    public static function getPublishedGames() {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_games . '` 
                                 WHERE `published` = \'1\'');
        
        $mysql->close();

        $gameList = array();
        
        while ($object = $result->fetch_object('Game')) {
            array_push($gameList, $object);
        }

        return $gameList;
    }
    
    /* 
     * Create a new game.
     */
    public static function createGame($name, $title, $price, $mode, $description, $startTime, $endTime, $published) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_main_games . '` (`name`, `title`, `price`, `mode`, `description`, `startTime`, `endTime`, `published`) 
                            VALUES (\'' . $mysql->real_escape_string($name) . '\', 
                                    \'' . $mysql->real_escape_string($title) . '\', 
                                    \'' . $mysql->real_escape_string($price) . '\', 
                                    \'' . $mysql->real_escape_string($mode) . '\', 
                                    \'' . $mysql->real_escape_string($description) . '\', 
                                    \'' . $mysql->real_escape_string($startTime) . '\', 
                                    \'' . $mysql->real_escape_string($endTime) . '\', 
                                    \'' . $mysql->real_escape_string($published) . '\');');
        
        $mysql->close();
    }
    
    /* 
     * Update information about a game.
     */
    public static function updateGame($id, $name, $title, $price, $mode, $description, $startTime, $endTime, $published) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_main_games . '` 
                            SET `name` = \'' . $mysql->real_escape_string($name) . '\', 
                                `title` = \'' . $mysql->real_escape_string($title) . '\', 
                                `price` = \'' . $mysql->real_escape_string($price) . '\', 
                                `mode` = \'' . $mysql->real_escape_string($mode) . '\', 
                                `description` = \'' . $mysql->real_escape_string($description) . '\', 
                                `startTime` = \'' . $mysql->real_escape_string($startTime) . '\', 
                                `endTime` = \'' . $mysql->real_escape_string($endTime) . '\', 
                                `published` = \'' . $mysql->real_escape_string($published) . '\'
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
    
    /*
     * Remove a game from the database.
     */
    public static function removeGame($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_main_games . '` 
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
}
?>