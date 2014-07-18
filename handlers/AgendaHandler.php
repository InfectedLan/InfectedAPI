<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/Settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/MySQL.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/objects/Agenda.php';

class AgendaHandler {
	public static function getAgenda($id) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_agenda . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new Agenda($row['id'], $row['datetime'], $row['name'], $row['description']);
		}
	}
	
	public static function getAgendas() {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_agenda . ' ORDER BY datetime');
		$agendaList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			array_push($agendaList, self::getAgenda($row['id']));
		}
		
		MySQL::close($con);
		
		return $agendaList;
	}
	
	public static function getAgendasBetween($first, $last) {
		$con = MySQL::open(Settings::db_name_infected);
		
		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_agenda . ' ORDER BY datetime');
		$agendaList = array();
		
		while ($row = mysqli_fetch_array($result)) {
			$agenda = self::getAgenda($row['id']);
			$now = date('U');
			
			if ($agenda->getDatetime() >= $now - $first * 60 * 60 ||
				$agenda->getDatetime() + (60*90) >= $now + $last * 60 * 60) {
				array_push($agendaList, $agenda);
			}
		}
		
		MySQL::close($con);
		
		return $agendaList;
	}
}
?>