<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/page.php';

class PageHandler {
    public static function getPage($id) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '` 
                                    WHERE id=\'' . $database->real_escape_string($id) . '\';');
        
        $database->close();

		return $result->fetch_object('Page');
    }
    
    public static function getPageByName($name) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '`
                                    WHERE `name` = \'' . $database->real_escape_string($name) . '\';');
        
        $database->close();

        return $result->fetch_object('Page');
    }
    
    // Get a list of all pages
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