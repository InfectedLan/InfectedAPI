<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/tickettype.php';

class TicketTypeHandler {
	public static function getTicketType($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_infected_tickets_ticketTypes . ' WHERE id=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if ($row) {
			return new TicketType($row['id'], $row['humanName']);
		}
	}
}
?>