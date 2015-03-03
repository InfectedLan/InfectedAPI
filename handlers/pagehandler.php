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
require_once 'objects/page.php';

class PageHandler {
    /*
     * Return the page bu the internal id.
     */
    public static function getPage($id) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '` 
                                    WHERE id=\'' . $database->real_escape_string($id) . '\';');
        
        $database->close();

		    return $result->fetch_object('Page');
    }
    
    /*
     * Return the page by name.
     */
    public static function getPageByName($name) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '`
                                    WHERE `name` = \'' . $database->real_escape_string($name) . '\';');
        
        $database->close();

        return $result->fetch_object('Page');
    }
    
    /*
     * Returns a list of all pages.
     */
    public static function getPages() {
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '`;');
              
        $database->close();

        $pageList = array();
        
        while ($object = $result->fetch_object('Page')) {
            array_push($pageList, $object);
        }

        return $pageList;
    }
    
    /* 
     * Create a new page.
     */
    public static function createPage($name, $title, $content) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_main_pages . '` (`name`, `title`, `content`) 
                          VALUES (\'' . $database->real_escape_string($name) . '\', 
                                  \'' . $database->real_escape_string($title) . '\', 
                                  \'' . $database->real_escape_string($content) . '\')');
        
        $database->close();
    }
    
    /*
     * Update a page.
     */
    public static function updatePage(Page $page, $title, $content) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $database->query('UPDATE `' . Settings::db_table_infected_main_pages . '` 
                          SET `title` = \'' . $database->real_escape_string($title) . '\', 
                              `content` = \'' . $database->real_escape_string($content) . '\' 
                          WHERE `id` = \'' . $page->getId() . '\';');
        
        $database->close();
    }
    
    /*
     * Remove a page.
     */
    public static function removePage(Page $page) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_main_pages . '` 
                          WHERE `id` = \'' . $page->getId() . '\';');
        
        $database->close();
    }
}
?>