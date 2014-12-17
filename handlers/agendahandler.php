<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/agenda.php';

class AgendaHandler {
    public static function getAgenda($id) {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_main_agenda . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                      
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        if ($row) {
            return new Agenda($row['id'], 
                              $row['event'], 
                              $row['name'], 
                              $row['title'], 
                              $row['description'],
                              $row['start']);
        }
    }
    
    public static function getAgendas() {
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_main_agenda . '`
                                      WHERE `event` = ' . EventHandler::getCurrentEvent()->getId() . '
                                      ORDER BY `start`;');
                                      
        $agendaList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($agendaList, self::getAgenda($row['id']));
        }
        
        $mysql->close();
        
        return $agendaList;
    }
    
    public static function getAgendaSelection($first, $last) {    
        $mysql = MySQL::open(Settings::db_name_infected_main);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_main_agenda . '`
                                      WHERE `start` 
                                      BETWEEN ' . $mysql->real_escape_string($first) . ' 
                                      AND ' . $mysql->real_escape_string($last) . '
                                      ORDER BY `start`;'); 
                                      
        $agendaList = array();
        
        while ($row = mysqli_fetch_array($result)) {
            array_push($agendaList, self::getAgenda($row['id']));
        }
        
        $mysql->close();
        
        return $agendaList;
    }
}
?>