<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/page.php';

class PageHandler {
    public static function getPage($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '` 
                                 WHERE id=\'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();

		return $result->fetch_object('Page');
    }
    
    public static function getPageByName($name) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '`
                                 WHERE `name` = \'' . $mysql->real_escape_string($name) . '\';');
        
        $mysql->close();

        return $result->fetch_object('Page');
    }
    
    // Get a list of all pages
    public static function getPages() {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '`;');
              
        $mysql->close();

        $pageList = array();
        
        while ($object = $result->fetch_object('Page')) {
            array_push($pageList, $object);
        }

        return $pageList;
    }
    
    /* 
     * Create a new page.
     */
    public static function createPage($name, $title, $mysqltent) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_main_pages . '` (`name`, `title`, `content`) 
                       VALUES (\'' . $mysql->real_escape_string($name) . '\', 
                               \'' . $mysql->real_escape_string($title) . '\', 
                               \'' . $mysql->real_escape_string($mysqltent) . '\')');
        
        $mysql->close();
    }
    
    /*
     * Update a page.
     */
    public static function updatePage($id, $title, $content) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_main_pages . '` 
                       SET `title` = \'' . $mysql->real_escape_string($title) . '\', 
                           `content` = \'' . $mysql->real_escape_string($content) . '\' 
                       WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
    
    /*
     * Remove a page.
     */
    public static function removePage($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_main_pages . '` 
                       WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
}
?>