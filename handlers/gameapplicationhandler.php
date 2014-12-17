<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/gameapplication.php';

class GameApplicationHandler {
    public static function getGameApplication($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_gameapplications . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                        
        $row = $result->fetch_array();
        
        $mysql->close();
        
        if ($row) {
            return new GameApplication($row['id'], 
                                       $row['eventId'], 
                                       $row['gameId'], 
                                       $row['name'], 
                                       $row['tag'], 
                                       $row['contactname'], 
                                       $row['contactnick'], 
                                       $row['phone'], 
                                       $row['email']);
        }
    }
    
    public static function getGameApplications($game) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_main_gameapplications . '` 
                                      WHERE `gameId` = \'' . $mysql->real_escape_string($game->getId()) . '\';');
                                    
        $gameApplicationList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($gameApplicationList, self::getGameApplication($row['id']));
        }
        
        $mysql->close();
        
        return $gameApplicationList;
    }
    
    public static function getGameApplicationsForEvent($game, $event) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_main_gameapplications . '` 
                                      WHERE `eventId` = \'' . $mysql->real_escape_string($event->getId()) . '\'
                                      AND `gameId` = \'' . $mysql->real_escape_string($game->getId()) . '\';');
                                    
        $gameApplicationList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($gameApplicationList, self::getGameApplication($row['id']));
        }
        
        $mysql->close();
        
        return $gameApplicationList;
    }
    
    public static function createGameApplication($event, $game, $name, $tag, $mysqltactname, $mysqltactnick, $phone, $email) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_main_gameapplications . '` (`eventId`, `gameId`, `name`, `tag`, `contactname`, `contactnick`, `phone`, `email`) 
                            VALUES (\'' . $mysql->real_escape_string($event->getId()) . '\', 
                                    \'' . $mysql->real_escape_string($game->getId()) . '\', 
                                    \'' . $mysql->real_escape_string($name) . '\', 
                                    \'' . $mysql->real_escape_string($tag) . '\', 
                                    \'' . $mysql->real_escape_string($mysqltactname) . '\', 
                                    \'' . $mysql->real_escape_string($mysqltactnick) . '\', 
                                    \'' . $mysql->real_escape_string($phone) . '\', 
                                    \'' . $mysql->real_escape_string($email) . '\');');
        
        $mysql->close();
    }
}
?>