<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'database.php';
require_once 'objects/game.php';

class GameHandler {
    /*
     * Get a game by the internal id.
     */
    public static function getGame($id) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_games . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
        $database->close();
		
		    return $result->fetch_object('Game');
    }
    
    public static function getGames() {
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_games . '`;');
                   
        $database->close();

        $gameList = array();
        
        while ($object = $result->fetch_object('Game')) {
            array_push($gameList, $object);
        }
        
        return $gameList;
    }
    
    public static function getPublishedGames() {
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_games . '` 
                                 WHERE `published` = \'1\'');
        
        $database->close();

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
        $database = Database::open(Settings::db_name_infected_main);
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_main_games . '` (`name`, `title`, `price`, `mode`, `description`, `startTime`, `endTime`, `published`) 
                          VALUES (\'' . $database->real_escape_string($name) . '\', 
                                  \'' . $database->real_escape_string($title) . '\', 
                                  \'' . $database->real_escape_string($price) . '\', 
                                  \'' . $database->real_escape_string($mode) . '\', 
                                  \'' . $database->real_escape_string($description) . '\', 
                                  \'' . $database->real_escape_string($startTime) . '\', 
                                  \'' . $database->real_escape_string($endTime) . '\', 
                                  \'' . $database->real_escape_string($published) . '\');');
        
        $database->close();
    }
    
    /* 
     * Update information about a game.
     */
    public static function updateGame(Game $game, $name, $title, $price, $mode, $description, $startTime, $endTime, $published) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $database->query('UPDATE `' . Settings::db_table_infected_main_games . '` 
                          SET `name` = \'' . $database->real_escape_string($name) . '\', 
                              `title` = \'' . $database->real_escape_string($title) . '\', 
                              `price` = \'' . $database->real_escape_string($price) . '\', 
                              `mode` = \'' . $database->real_escape_string($mode) . '\', 
                              `description` = \'' . $database->real_escape_string($description) . '\', 
                              `startTime` = \'' . $database->real_escape_string($startTime) . '\', 
                              `endTime` = \'' . $database->real_escape_string($endTime) . '\', 
                              `published` = \'' . $database->real_escape_string($published) . '\'
                          WHERE `id` = \'' . $game->getId() . '\';');
        
        $database->close();
    }
    
    /*
     * Remove a game from the database.
     */
    public static function removeGame(Game $game) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_main_games . '` 
                          WHERE `id` = \'' . $game->getId() . '\';');
        
        $database->close();
    }
}
?>