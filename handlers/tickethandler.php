<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/ticket.php';
require_once 'objects/event.php';
require_once 'objects/user.php';
require_once 'objects/tickettype.php';
require_once 'objects/seat.php';
require_once 'objects/payment.php';

class TicketHandler {
	/*
	 * Return the ticket by the internal id.
	 */
	public static function getTicket($id) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('Ticket');
	}

	/*
	 * Returns true if the specified user has a ticket.
	 */
	public static function hasTicketByUserAndEvent(User $user, Event $event) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `userId` = \'' . $user->getId() . '\'
																LIMIT 1;');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if the specified user has a ticket.
	 */
	public static function hasTicketByUser(User $user) {
		return self::hasTicketByUserAndEvent($user, EventHandler::getCurrentEvent());
	}

	/*
	 * Returns the specified user latest ticket from the specified event.
	 */
	public static function getTicketByUserAndEvent(User $user, Event $event) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `userId` = \'' . $user->getId() . '\'
																LIMIT 1;');

		$database->close();

		return $result->fetch_object('Ticket');
	}

	/*
	 * Return the specified user latest ticket.
	 */
	public static function getTicketByUser(User $user) {
		return self::getTicketByUserAndEvent($user, EventHandler::getCurrentEvent());
	}

	/*
	 * Returns a list of the specified user tickets for the specified event.
	 */
	public static function getTicketsByUserAndEvent(User $user, Event $event) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `userId` = \'' . $user->getId() . '\';');

		$database->close();

		$ticketList = [];

		while ($object = $result->fetch_object('Ticket')) {
			$ticketList[] = $object;
		}

		return $ticketList;
	}

	/*
	 * Returns a list of the specified user tickets for the current event..
	 */
	public static function getTicketsByUser(User $user) {
		return self::getTicketsByUserAndEvent($user, EventHandler::getCurrentEvent());
	}

	/*
	 * Returns a list of all the tickets for a specified event.
	 */
	public static function getTicketsByEvent(Event $event) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `eventId` = \'' . $event->getId() . '\';');

		$database->close();

		$ticketList = [];

		while ($object = $result->fetch_object('Ticket')) {
			$ticketList[] = $object;
		}

		return $ticketList;
	}

	/*
	 * Returns a list of all the tickets.
	 */
	public static function getTickets() {
		return self::getTicketsByEvent(EventHandler::getCurrentEvent());
	}

	/*
	 * Returns a list of all the tickets from all events.
	 */
	public static function hasTicketsByUserAndAllEvents(User $user) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns a list of all the tickets from all events.
	 */
	public static function getTicketsByUserAndAllEvents(User $user) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		$database->close();

		$ticketList = [];

		while ($object = $result->fetch_object('Ticket')) {
			$ticketList[] = $object;
		}

		return $ticketList;
	}

	/*
	 * Returns true if the specified user is able to seat tickets.
	 */
	public static function getTicketsSeatableByUserAndEvent(User $user, Event $event) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND (`seaterId` = \'' . $user->getId() . '\' OR (`userId` = \'' . $user->getId() . '\' AND `seaterId` = \'0\'));');

		$database->close();

		$ticketList = [];

		while ($object = $result->fetch_object('Ticket')) {
			$ticketList[] = $object;
		}

		return $ticketList;
	}

	/*
	 * Returns true if the specified user is able to seat tickets.
	 */
	public static function getTicketsSeatableByUser(User $user) {
		return self::getTicketsSeatableByUserAndEvent($user, EventHandler::getCurrentEvent());
	}

	/*
	 * Create a new ticket.
	 */
	public static function createTicket(User $user, TicketType $ticketType, Payment $payment) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('INSERT INTO `' . Settings::db_table_infected_tickets_tickets . '` (`eventId`, `typeId`, `buyerId`, `paymentId`, `userId`)
																VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
																				\'' . $ticketType->getId() . '\',
																				\'' . $user->getId() . '\',
																				\'' . $payment->getId() . '\',
																				\'' . $user->getId() . '\');');

		$ticket = self::getTicket($database->insert_id);

		$database->close();

		return $ticket;
	}

	/*
	 * Update the ticket with the specified user.
	 */
	public static function updateTicketUser(Ticket $ticket, User $user) {
		if (!$user->equals($ticket->getUser())) {
			$database = Database::open(Settings::db_name_infected_tickets);

			// Change the user of the ticket.
			$database->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '`
											  SET `userId` = \'' . $user->getId() . '\'
											  WHERE `id` = \'' . $ticket->getId() . '\';');

			$database->close();
		}
	}

	/*
	 * Update the ticket with the specified seater.
	 */
	public static function updateTicketSeater(Ticket $ticket, User $seater) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$database->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '`
										  SET `seaterId` = \'' . ($seater != null ? $seater->getId() : 0) . '\'
										  WHERE `id` = \'' . $ticket->getId() . '\';');

		$database->close();
	}

	/*
	 * Update the ticket with the specified seat.
	 */
	public static function updateTicketSeat(Ticket $ticket, Seat $seat) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('UPDATE `' . Settings::db_table_infected_tickets_tickets . '`
																SET `seatId` = \'' . ($seat != null ? $seat->getId() : 0) . '\'
																WHERE `id` = \'' . $ticket->getId() . '\';');

		$database->close();
	}

	/*
	 * Removes the specified ticket.
	 */
	public static function removeTicket(Ticket $ticket) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('DELETE FROM `' . Settings::db_table_infected_tickets_storesessions . '`
																WHERE `id` = \'' . $storeSession->getId() . '\';');

		$database->close();
	}

	/*
	 * Check in a ticket.
	 */
	public static function checkInTicket(Ticket $ticket) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$reuslt = $database->query('INSERT INTO `' . Settings::db_table_infected_tickets_checkedintickets . '` (`ticketId`)
																VALUES (\'' . $ticket->getId() . '\');');

		$database->close();
	}

	/*
	 * Returns true if the specified ticket is checked in.
	 */
	public static function isTicketCheckedIn(Ticket $ticket) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_checkedintickets . '`
																WHERE `ticketId` = \'' . $ticket->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}
}
?>
