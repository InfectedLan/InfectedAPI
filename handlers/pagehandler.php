<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/page.php';

class PageHandler {
    // Get page.
    public static function getPage($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_pages . '` 
                                      WHERE id=\'' . $mysql->real_escape_string($id) . '\';');
                                      
        $row = mysqli_fetch_array($result);
        
        $mysql->close();

        if ($row) {
            return new Page($row['id'], 
                            $row['name'], 
                            $row['title'], 
                            $row['content']);
        }
    }
    
    // Get page.
    public static function getPageByName($name) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_main_pages . '`
                                      WHERE `name` = \'' . $mysql->real_escape_string($name) . '\';');
                                      
        $row = mysqli_fetch_array($result);
        
        $mysql->close();

        if ($row) {
            return self::getPage($row['id']);
        }
    }
    
    // Get a list of all pages
    public static function getPages() {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_main_pages . '`;');
                                      
        $pageList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($pageList, self::getPage($row['id']));
        }
        
        $mysql->close();

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
     * Remove a page.
     */
    public static function removePage($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_main_pages . '` 
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
    
    /*
     * Update a page.
     */
    public static function updatePage($id, $name, $title, $mysqltent) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_main_pages . '` 
                            SET `name` = \'' . $mysql->real_escape_string($name) . '\', 
                                `title` = \'' . $mysql->real_escape_string($title) . '\', 
                                `content` = \'' . $mysql->real_escape_string($mysqltent) . '\' 
                            WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $mysql->close();
    }
}
?>