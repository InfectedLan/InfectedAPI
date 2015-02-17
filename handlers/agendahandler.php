<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/agenda.php';
require_once 'objects/event.php';

class AgendaHandler {
    /*
     * Get an agenda by the internal id.
     */
    public static function getAgenda($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
  		$mysql->close();
  		  
  		return $result->fetch_object('Agenda');
    }
    
    /*
	   * Returns agendas.
	   */
    public static function getAgendas() {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
                                 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                 ORDER BY `startTime`;');
		
        $mysql->close();
		
        $agendaList = array();
        
        while ($object = $result->fetch_object('Agenda')) {
            array_push($agendaList, $object);
        }
        
        return $agendaList;
    }
	
  /*
   * Returns published agendas.
	 */
	public static function getPublishedAgendas() {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
								 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								 AND `published` = \'1\'
								 ORDER BY `startTime`;');
				
		$mysql->close();	

        $agendaList = array();
        
        while ($object = $result->fetch_object('Agenda')) {
            array_push($agendaList, $object);
        }
        
        return $agendaList;
    }
	
	/*
	 * Returns only published agendas that have not happend yet.
	 */
	public static function getPublishedNotHappendAgendas() {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
								 WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								 AND DATE_ADD(`startTime`, INTERVAL 1 HOUR) >= NOW()
								 AND `published` = \'1\'
								 ORDER BY `startTime`;');
								 
        $mysql->close();	
	   
        $agendaList = array();
        
        while ($object = $result->fetch_object('Agenda')) {
            array_push($agendaList, $object);
        }
        
        return $agendaList;
    }
    
    public static function getAgendaSelection($first, $last) {    
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
                                 WHERE `startTime` 
                                 BETWEEN ' . $mysql->real_escape_string($first) . ' 
                                 AND ' . $mysql->real_escape_string($last) . '
                                 ORDER BY `startTime`;');
		    
        $mysql->close();
		    
        $agendaList = array();
        
        while ($object = $result->fetch_object('Agenda')) {
            array_push($agendaList, $object);
        }
        
        return $agendaList;
    }
	
    /* 
     * Create a new agenda entry.
     */
    public static function createAgenda(Event $event, $name, $title, $description, $startTime) {
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
    public static function updateAgenda(Agenda $agenda, $title, $description, $startTime, $published) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('UPDATE `' . Settings::db_table_infected_main_agenda . '` 
                       SET `title` = \'' . $mysql->real_escape_string($title) . '\', 
                           `description` = \'' . $mysql->real_escape_string($description) . '\', 
                           `startTime` = \'' . $mysql->real_escape_string($startTime) . '\',
								           `published` = \'' . $mysql->real_escape_string($published) . '\'
                       WHERE `id` = \'' . $mysql->real_escape_string($agenda->getId()) . '\';');
        
        $mysql->close();
    }
	
    /*
     * Remove an agenda entry.
     */
    public static function removeAgenda(Agenda $agenda) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $mysql->query('DELETE FROM `' . Settings::db_table_infected_main_agenda . '` 
                       WHERE `id` = \'' . $mysql->real_escape_string($agenda->getId()) . '\';');
        
        $mysql->close();
    }
}
?>