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
		
		return $result->fetch_object('Slide');
    }
    
    public static function getSlides() {
        $mysql = MySQL::open(Settings::db_name_infected_info);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_info_slides . '`
								 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
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
	
	/* 
     * Create a new slide entry.
     */
    public static function createSlide($event, $name, $title, $content, $startTime, $endTime, $published) {
        $mysql = MySQL::open(Settings::db_name_infected_info);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_info_slides . '` (`eventId`, `name`, `title`, `content`, `startTime`, `endTime`, `published`) 
					   VALUES (\'' . $mysql->real_escape_string($event->getId()) . '\', 
							   \'' . $mysql->real_escape_string($name) . '\', 
							   \'' . $mysql->real_escape_string($title) . '\', 
							   \'' . $mysql->real_escape_string($content) . '\', 
							   \'' . $mysql->real_escape_string($startTime) . '\',
							   \'' . $mysql->real_escape_string($endTime) . '\',
							   \'' . $mysql->real_escape_string($published) . '\');');
        
        $mysql->close();
    }
    
	/*
     * Update a slide.
     */
    public static function updateSlide($slide, $title, $content, $startTime, $endTime, $published) {
        $mysql = MySQL::open(Settings::db_name_infected_info);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_info_slides . '` 
					   SET `title` = \'' . $mysql->real_escape_string($title) . '\', 
					       `content` = \'' . $mysql->real_escape_string($content) . '\', 
						   `startTime` = \'' . $mysql->real_escape_string($startTime) . '\',
						   `endTime` = \'' . $mysql->real_escape_string($endTime) . '\',
					       `published` = \'' . $mysql->real_escape_string($published) . '\'
					   WHERE `id` = \'' . $mysql->real_escape_string($slide->getId()) . '\';');
        
        $mysql->close();
    }
	
    /*
     * Remove an agenda entry.
     */
    public static function removeSlide($slide) {
        $mysql = MySQL::open(Settings::db_name_infected_info);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_info_slides . '` 
                       WHERE `id` = \'' . $mysql->real_escape_string($slide->getId()) . '\';');
        
        $mysql->close();
    }
}
?>