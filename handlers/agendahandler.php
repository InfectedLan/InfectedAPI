<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/agenda.php';
require_once 'objects/event.php';

class AgendaHandler {
    /*
     * Get an agenda by the internal id.
     */
    public static function getAgenda($id) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '` 
                                    WHERE `id` = \'' . $database->real_escape_string($id) . '\';');
        
  		$database->close();
  		  
  		return $result->fetch_object('Agenda');
    }
    
    /*
	   * Returns agendas.
	   */
    public static function getAgendas() {
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
                                    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
                                    ORDER BY `startTime`;');
		
        $database->close();
		
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
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
								    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								    AND `published` = \'1\'
								    ORDER BY `startTime`;');
				
		$database->close();	

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
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
								    WHERE `eventId` = \'' . EventHandler::getCurrentEvent()->getId() . '\'
								    AND DATE_ADD(`startTime`, INTERVAL 1 HOUR) >= NOW()
								    AND `published` = \'1\'
								    ORDER BY `startTime`;');
								 
        $database->close();	
	   
        $agendaList = array();
        
        while ($object = $result->fetch_object('Agenda')) {
            array_push($agendaList, $object);
        }
        
        return $agendaList;
    }
    
    public static function getAgendaSelection($first, $last) {    
        $database = Database::open(Settings::db_name_infected_main);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '`
                                    WHERE `startTime` 
                                    BETWEEN ' . $database->real_escape_string($first) . ' 
                                    AND ' . $database->real_escape_string($last) . '
                                    ORDER BY `startTime`;');
		    
        $database->close();
		    
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
        $database = Database::open(Settings::db_name_infected_main);
        
        $database->query('INSERT INTO `' . Settings::db_table_infected_main_agenda . '` (`eventId`, `name`, `title`, `description`, `startTime`) 
                          VALUES (\'' . $event->getId() . '\', 
    							  \'' . $database->real_escape_string($name) . '\', 
                                  \'' . $database->real_escape_string($title) . '\', 
                                  \'' . $database->real_escape_string($description) . '\', 
                                  \'' . $database->real_escape_string($startTime) . '\');');
        
        $database->close();
    }
    
    /*
     * Update an agenda.
     */
    public static function updateAgenda(Agenda $agenda, $title, $description, $startTime, $published) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $database->query('UPDATE `' . Settings::db_table_infected_main_agenda . '` 
                          SET `title` = \'' . $database->real_escape_string($title) . '\', 
                              `description` = \'' . $database->real_escape_string($description) . '\', 
                              `startTime` = \'' . $database->real_escape_string($startTime) . '\',
    						  `published` = \'' . $database->real_escape_string($published) . '\'
                          WHERE `id` = \'' . $agenda->getId() . '\';');
        
        $database->close();
    }
	
    /*
     * Remove an agenda entry.
     */
    public static function removeAgenda(Agenda $agenda) {
        $database = Database::open(Settings::db_name_infected_main);
        
        $database->query('DELETE FROM `' . Settings::db_table_infected_main_agenda . '` 
                          WHERE `id` = \'' . $agenda->getId() . '\';');
        
        $database->close();
    }
}
?>