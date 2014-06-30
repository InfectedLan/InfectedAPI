<?php
require_once '/../Settings.php';
require_once '/../MySQL.php';
require_once '/../objects/Ticket.php';
require_once 'EventHandler.php';
require_once 'TicketTypeHandler.php';
require_once 'SeatHandler.php';

class TicketHandler {
	public static function getTicket($id)
	{
		$con = MySQL::open(Settings::db_name_tickets);

		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_seats . ' WHERE ID=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row)
		{
			return new Ticket($row['id'], EventHandler::getEvent($row['event']), UserHandler::getUser($row['owner']), 
				TicketTypeHandler::getTicketType($row['type']), SeatHandler::getSeat($row['seat']), 
				UserHandler::getUser($row['user']), UserHandler::getUser($row['seater'])
				);
		}
	}
	public static function getAvailableTickets()
	{
		$currentEvent = EventHandler::getCurrentEvent();//Todo: get current event, return amount of tickets left

		$tickets = self::getTicketsForEvent($currentEvent->getId());
		$numTickets = count($tickets);

		return $currentEvent->getParticipants()-$numTickets;
	}
	public static function getTicketsForEvent($eventid)
	{
		$con = MySQL::open(Settings::db_name_tickets);

		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_tickets . ' WHERE event=\'' . $eventid . '\'');

		$ticketList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($ticketList, self::getTicket($row['id']));
		}

		MySQL::close($con);

		return $ticketList;
	}
}
?>