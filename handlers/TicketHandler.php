<?php
require_once '/../Settings.php';
require_once '/../MySQL.php';
require_once '/../objects/Ticket.php';
require_once 'EventHandler.php';

class TicketHandler {
	public static function getTicket($id)
	{
		$con = MySQL::open(Settings::db_name_tickets);

		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_seats . ' WHERE ID=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row)
		{
			return new Ticket($row['id'], EventHandler::getEvent($row['event']), UserHandler::getUser($row['owner'], 
				TicketTypeHandler::getTicketType($row['type']), /* TODO complete this */
				)
		}
	}
}
?>