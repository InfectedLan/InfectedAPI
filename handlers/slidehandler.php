<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/slide.php';
require_once 'objects/event.php';

class SlideHandler {
    /*
     * Get a slide by the internal id.
     */
    public static function getSlide($id) {
        $database = Database::open(Settings::db_name_infected_info);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_info_slides . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
		$database->close();
		
		return $result->fetch_object('Slide');
    }
    
    /*
     * Get a list of all slides.
     */
    public static function getSlides() {
        $database = Database::open(Settings::db_name_infected_info);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_info_slides . '`
								    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                    ORDER BY `startTime`;');
		
		$database->close();
		
        $slideList = array();
        
        while ($object = $result->fetch_object('Slide')) {
            array_push($slideList, $object);
        }
        
        return $slideList;
    }
	
    /*
     * Get a list of all published slides.
     */
	public static function getPublishedSlides() {
        $database = Database::open(Settings::db_name_infected_info);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_info_slides . '`
                                    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								    AND `startTime` <= NOW()
                                    AND `endTime` >= NOW()
                                    AND `published` = \'1\'
                                    ORDER BY `startTime`;');
        
		$database->close();
		
        $slideList = array();
        
        while ($object = $result->fetch_object('Slide')) {
            array_push($slideList, $object);
        }
        
        return $slideList;
    }
	
	/* 
     * Create a new slide entry.
     */
    public static function createSlide(Event $event, $name, $title, $content, $startTime, $endTime, $published) {
        $database = Database::open(Settings::db_name_infected_info);
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_info_slides . '` (`eventId`, `name`, `title`, `content`, `startTime`, `endTime`, `published`) 
					      VALUES (\'' . $event->getId() . '\', 
							      \'' . $database->real_escape_string($name) . '\', 
							      \'' . $database->real_escape_string($title) . '\', 
							      \'' . $database->real_escape_string($content) . '\', 
							      \'' . $database->real_escape_string($startTime) . '\',
							      \'' . $database->real_escape_string($endTime) . '\',
							      \'' . $database->real_escape_string($published) . '\');');
        
        $database->close();
    }
    
	/*
     * Update a slide.
     */
    public static function updateSlide(Slide $slide, $title, $content, $startTime, $endTime, $published) {
        $database = Database::open(Settings::db_name_infected_info);
        
        $database->query('UPDATE `' . Settings::db_table_infected_info_slides . '` 
					      SET `title` = \'' . $database->real_escape_string($title) . '\', 
					          `content` = \'' . $database->real_escape_string($content) . '\', 
						      `startTime` = \'' . $database->real_escape_string($startTime) . '\',
						      `endTime` = \'' . $database->real_escape_string($endTime) . '\',
					          `published` = \'' . $database->real_escape_string($published) . '\'
					      WHERE `id` = \'' . $slide->getId() . '\';');
        
        $database->close();
    }
	
    /*
     * Remove a slide.
     */
    public static function removeSlide(Slide $slide) {
        $database = Database::open(Settings::db_name_infected_info);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_info_slides . '` 
                          WHERE `id` = \'' . $slide->getId() . '\';');
        
        $database->close();
    }
}
?>