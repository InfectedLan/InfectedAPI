<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'handlers/seathandler.php';
require_once 'handlers/storesessionhandler.php';
require_once 'objects/ticket.php';

class TicketHandler {
	public static function getTicket($id) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '` 
									  WHERE `id` = \'' . $id . '\';');
									
		$row = mysqli_fetch_array($result);

		MySQL::close($con);

		if ($row) {
			return new Ticket($row['id'],
							  $row['eventId'], 
							  $row['ownerId'], 
							  $row['typeId'], 
							  $row['seatId'],
							  $row['seaterId']);
		}
	}
	
	// TODO: Move this to Event.
	public static function getAvailableTickets() {
		return self::getAvailableTicketsForEvent(EventHandler::getCurrentEvent());
	}
	
	public static function getAvailableTicketsForEvent($event) {
		$ticketList = self::getTicketsForEvent($event->getId());

		return $event->getParticipants() - count($ticketList);
	}
	
	public static function getTicketList($event) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
									  WHERE `eventId` = \'' . $event->getId() . '\';');

		$ticketList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($ticketList, self::getTicket($row['id']));
		}

		MySQL::close($con);

		return $ticketList;
	}

	public static function getTicketCount($event) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
									  WHERE `eventId` = \'' . $event->getId() . '\';');
		MySQL::close($con);

		return mysqli_num_rows($result);
	}
	
	public static function getTicketForUser($user) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
									  WHERE `ownerId` = \'' . $user->getId() . '\'
									  AND `eventId` = \'' .  EventHandler::getCurrentEvent()->getId() . '\' 
									  LIMIT 1;');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);
		
		if ($row) {
			return self::getTicket($row['id']);
		}
	}
	
	public static function hasTicket($user) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
									  WHERE `ownerId` = \'' . $user->getId() . '\'
									  AND `eventId` = \'' .  EventHandler::getCurrentEvent()->getId() . '\';');

		$row = mysqli_fetch_array($result);

		MySQL::close($con);
		
		return $row ? true : false;
	}
	
	public static function getTicketsForOwner($user) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '` 
									  WHERE `ownerId` = \'' . $user->getId() . '\';');

		$ticketList = array();

		while($row = mysqli_fetch_array($result)) {
			array_push($ticketList, self::getTicket($row['id']));
		}

		MySQL::close($con);

		return $ticketList;
	}
	
	public static function transferTicket($ticket, $newOwner) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
									  SET `ownerId` = \'' . $newOwner->getId() . '\' 
									  WHERE `id` = \'' . $ticket->getId() . '\';');
 
		MySQL::close($con);
	}

	public static function setSeater($ticket, $newSeater) {
		$con = MySQL::open(Settings::db_name_infected_tickets);

		if (!isset($newSeater) || empty($newSeater)) {
			$result = mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
										  SET `seaterId` = \'0\' 
										  WHERE `id` = \'' . $ticket->getId() . '\';');
		} else {
			$result = mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_tickets_tickets . '` 
										  SET `seaterId` = \'' . $newSeater->getId() . '\' 
										  WHERE `id` = \'' . $ticket->getId() . '\';');
		}
		
		MySQL::close($con);
	}

	public static function createTicket($user, $ticketType)
	{
		$currentEvent = EventHandler::getCurrentEvent();

		$con = MySQL::open(Settings::db_name_infected_tickets);

		$result = mysqli_query($con, 'INSERT INTO `' . Settings::db_table_infected_tickets_tickets . '` (`ownerId`, `eventId`, `typeId`) VALUES (' . $user->getId() . ', ' . $currentEvent->getId() . ', ' . $ticketType->getId() . ');');

		MySQL::close($con);
	}

}
?>