<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/agenda.php';

class AgendaHandler {
    public static function getAgenda($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                      
        $row = $result->fetch_array();
        
        $mysql->close();
        
        if ($row) {
            return new Agenda($row['id'], 
                              $row['eventId'], 
                              $row['name'], 
                              $row['title'], 
                              $row['description'],
                              $row['startTime']);
        }
    }
    
    public static function getAgendas() {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_main_agenda . '`
                                 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                 ORDER BY `startTime`;');
                                      
        $agendaList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($agendaList, self::getAgenda($row['id']));
        }
        
        $mysql->close();
        
        return $agendaList;
    }
    
    public static function getAgendaSelection($first, $last) {    
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_main_agenda . '`
                                      WHERE `startTime` 
                                      BETWEEN ' . $mysql->real_escape_string($first) . ' 
                                      AND ' . $mysql->real_escape_string($last) . '
                                      ORDER BY `startTime`;'); 
                                      
        $agendaList = array();
        
        while ($row = $result->fetch_array()) {
            array_push($agendaList, self::getAgenda($row['id']));
        }
        
        $mysql->close();
        
        return $agendaList;
    }
	
	/* 
     * Create a new agenda entry.
     */
    public static function createAgenda($event, $name, $title, $description, $startTime) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('INSERT INTO `' . Settings::db_table_infected_main_agenda . '` (`eventId`, `name`, `title`, `description`, `startTime`) 
                            VALUES (\'' . $mysql->real_escape_string($event->getId()) . '\', 
									\'' . $mysql->real_escape_string($name) . '\', 
                                    \'' . $mysql->real_escape_string($title) . '\', 
                                    \'' . $mysql->real_escape_string($description) . '\', 
                                    \'' . $mysql->real_escape_string($startTime) . '\');');
        
        $mysql->close();
    }
    
	/*
     * Update an agenda.
     */
    public static function updateAgenda($agenda, $title, $description, $startTime) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_main_agenda . '` 
                            SET `title` = \'' . $mysql->real_escape_string($title) . '\', 
                                `description` = \'' . $mysql->real_escape_string($description) . '\', 
                                `startTime` = \'' . $mysql->real_escape_string($startTime) . '\' 
                            WHERE `id` = \'' . $mysql->real_escape_string($agenda->getId()) . '\';');
        
        $mysql->close();
    }
	
    /*
     * Remove an agenda entry.
     */
    public static function removeAgenda($agenda) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_main_agenda . '` 
                       WHERE `id` = \'' . $mysql->real_escape_string($agenda->getId()) . '\';');
        
        $mysql->close();
    }
}
?>