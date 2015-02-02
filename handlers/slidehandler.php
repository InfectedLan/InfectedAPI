<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/slide.php';

class SlideHandler {
    public static function getSlide($id) {
        $mysql = MySQL::open(Settings::db_name_infected_info);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_info_slides . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
		$mysql->close();
		
        $row = $result->fetch_array();
        
        if ($row) {
            return new Slide($row['id'], 
                             $row['eventId'], 
                             $row['name'], 
                             $row['title'], 
                             $row['content'], 
                             $row['startTime'], 
							 $row['endTime'], 
							 $row['published']);
        }
    }
    
    public static function getSlides() {
        $mysql = MySQL::open(Settings::db_name_infected_info);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_info_slides . '`
								 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                 AND `startTime` <= NOW()
                                 AND `endTime` >= NOW()
                                 ORDER BY `startTime`;');
		
		$mysql->close();
		
        $slideList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($slideList, self::getSlide($row['id']));
        }
        
        return $slideList;
    }
	
	public static function getPublishedSlides() {
        $mysql = MySQL::open(Settings::db_name_infected_info);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_info_slides . '`
                                 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								 AND `startTime` <= NOW()
                                 AND `endTime` >= NOW()
                                 AND `published` = \'1\'
                                 ORDER BY `startTime`;');
        
		$mysql->close();
		
        $slideList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($slideList, self::getSlide($row['id']));
        }
        
        return $slideList;
    }
}
?>