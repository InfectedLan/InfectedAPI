<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/slide.php';

class SlideHandler {
    public static function getSlide($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_slides . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        if ($row) {
            return new Slide($row['id'], 
                             $row['start'], 
                             $row['end'], 
                             $row['title'], 
                             $row['content'], 
                             $row['published']);
        }
    }
    
    public static function getSlides() {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_main_slides . '`
                                      WHERE `start` <= NOW()
                                      AND `end` >= NOW()
                                      AND `published` = 1
                                      ORDER BY `start`;');
                                      
        $slideList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($slideList, self::getSlide($row['id']));
        }
        
        $mysql->close();
        
        return $slideList;
    }
}
?>