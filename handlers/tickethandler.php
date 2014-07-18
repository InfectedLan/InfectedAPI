<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/settings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/mysql.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/eventhandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/tickettypehandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/seathandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/objects/ticket.php';

class TicketHandler {
	public static function getTicket($id) {
		$con = MySQL::open(Settings::db_name_tickets);

		$result = mysqli_query($con, 'SELECT * FROM ' . Settings::db_table_tickets . ' WHERE ID=\'' . $id . '\'');
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if($row) {
			return new Ticket($row['id'], EventHandler::getEvent($row['event']), UserHandler::getUser($row['owner']), 
				TicketTypeHandler::getTicketType($row['type']), SeatHandler::getSeat($row['seat']), 
				UserHandler::getUser($row['seater'])
				);
		}
	}
	
	public static function getAvailableTickets() {
		$currentEvent = EventHandler::getCurrentEvent();//Todo: get current event, return amount of tickets left

		$tickets = self::getTicketsForEvent($currentEvent->getId());
		$numTickets = count($tickets);

		return $currentEvent->getParticipants()-$numTickets;
	}
	
	public static function getTicketsForEvent($eventId) {
		$con = MySQL::open(Settings::db_name_tickets);

		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_tickets . ' WHERE event=\'' . $eventId . '\'');

		$ticketList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($ticketList, self::getTicket($row['id']));
		}

		MySQL::close($con);

		return $ticketList;
	}
	
	public static function getTicketsForOwner($user) {
		$con = MySQL::open(Settings::db_name_tickets);

		$result = mysqli_query($con, 'SELECT id FROM ' . Settings::db_table_tickets . ' WHERE owner=\'' . $user->getId() . '\'');

		$ticketList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($ticketList, self::getTicket($row['id']));
		}

		MySQL::close($con);

		return $ticketList;
	}
	
	public static function transferTicket($ticket, $newOwner) {
		$con = MySQL::open(Settings::db_name_tickets);

		$result = mysqli_query($con, 'UPDATE ' . Settings::db_table_tickets . ' SET owner=\'' . $newOwner->getId() . '\' WHERE id=\'' . $ticket->getId() . '\'');

		MySQL::close($con);
	}

	public static function setSeater($ticket, $newSeater)
	{
		$con = MySQL::open(Settings::db_name_tickets);

		if(!isset($newSeater) || empty($newSeater))
		{
			$result = mysqli_query($con, 'UPDATE ' . Settings::db_table_tickets . ' SET seater=\'0\' WHERE id=\'' . $ticket->getId() . '\'');
		}
		else
		{
			$result = mysqli_query($con, 'UPDATE ' . Settings::db_table_tickets . ' SET seater=\'' . $newSeater->getId() . '\' WHERE id=\'' . $ticket->getId() . '\'');
		}
		MySQL::close($con);
	}
}
?>