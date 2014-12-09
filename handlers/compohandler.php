<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/compo.php';
require_once 'handlers/clanhandler.php';

class CompoHandler {
	public static function getCompo($id) {
		$con = MySQL::open(Settings::db_name_infected_compo);
		
		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_compos . '` 
									  WHERE `id` = \'' . $con->real_escape_string($id) . '\';');
		
		$row = mysqli_fetch_array($result);
		
		MySQL::close($con);
		
		if ($row) {
			return new Compo($row['id'], 
							 $row['startTime'], 
							 $row['registrationDeadline'], 
							 $row['name'],
							 $row['desc'], 
							 $row['event'], 
							 $row['teamSize'], 
							 $row['tag']);
		}
	}
	public static function getComposForEvent($event) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_compos . '` 
									  WHERE `event` = \'' . $event->getId() . '\';');

		$compoList = array();

		while ($row = mysqli_fetch_array($result)) {
			array_push($compoList, self::getCompo($row['id']));
		}

		MySQL::close($con);

		return $compoList;
	}

	public static function getClans($compo) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_compo_participantof . '` 
									  WHERE `compoId` = \'' . $con->real_escape_string($compo->getId()) . '\';');

		$clanList = array();

		while ($row = mysqli_fetch_array($result)) {
			$clan =  ClanHandler::getClan($row['clanId']);
			array_push($clanList, $clan);
		}

		MySQL::close($con);
		
		return $clanList;
	}
}
?>