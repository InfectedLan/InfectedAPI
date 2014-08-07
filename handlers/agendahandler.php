<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/agenda.php';

class AgendaHandler {
	public static function getAgenda($id) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_main_agenda . '` 
									  WHERE `id` = \'' . $id . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
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
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id`
									  FROM `' . Settings::db_table_infected_main_agenda . '`
									  ORDER BY `start`;');
									  
		$agendaList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($agendaList, self::getAgenda($row['id']));
		}
		
		MySQL::close($con);
		
		return $agendaList;
	}
	
	public static function getAgendaSelection($first, $last) {	
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id`
									  FROM `' . Settings::db_table_infected_main_agenda . '`
									  WHERE `start` 
									  BETWEEN ' . $first . ' 
									  AND ' . $last . '
									  ORDER BY `start`;'); 
									  
		$agendaList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($agendaList, self::getAgenda($row['id']));
		}
		
		MySQL::close($con);
		
		return $agendaList;
	}
}
?>