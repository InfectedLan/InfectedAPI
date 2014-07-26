<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/agenda.php';

class AgendaHandler {
	public static function getAgenda($id) {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT * 
								      FROM `' . Settings::db_table_infected_main_agenda . '` 
									  WHERE `id` = \'' . $id . '\';');
									  
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new Agenda($row['id'], 
							  $row['datetime'], 
							  $row['name'], 
							  $row['description']);
		}
	}
	
	public static function getAgendas() {
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id`
									  FROM `' . Settings::db_table_infected_main_agenda . '`
									  ORDER BY `datetime`;');
									  
		$agendaList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($agendaList, self::getAgenda($row['id']));
		}
		
		MySQL::close($con);
		
		return $agendaList;
	}
	
	public static function getAgendaSelection($first, $last) {
		$first = '2014-01-13 08:00:00';
		$last = '2014-02-18 23:00:00';
	
		$con = MySQL::open(Settings::db_name_infected_main);
		
		$result = mysqli_query($con, 'SELECT `id`
									  FROM `' . Settings::db_table_infected_main_agenda . '`
									  WHERE `datetime` 
									  BETWEEN ' . $first . ' 
									  AND ' . $last . '
									  ORDER BY `datetime`;'); 
									  
		$agendaList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($agendaList, self::getAgenda($row['id']));
		}
		
		MySQL::close($con);
		
		return $agendaList;
	}
}
?>