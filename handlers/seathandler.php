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
require_once 'handlers/tickethandler.php';
require_once 'handlers/rowhandler.php';
require_once 'objects/seat.php';

class SeatHandler {
	/*
	 * Get a seat by the internal id.
	 */
	public static function getSeat($id) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '`
									WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('Seat');
	}

	/*
	 * Returns a list of all seats.
	 */
	public static function getSeats() {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '`;');

		$database->close();

		$seatList = array();

		while ($object = $result->fetch_object('Seat')) {
			array_push($seatList, $object);
		}

		return $seatList;
	}

	/*
	 * Return all seats on the specified row.
	 */
	public static function getSeatsByRow(Row $row) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '`
									WHERE `rowId` = \'' . $row->getId() . '\';');

		$database->close();

		$seatList = array();

		while ($object = $result->fetch_object('Seat')) {
			array_push($seatList, $object);
		}

		return $seatList;
	}

	/*
	 * Add a seat to the specified row.
	 */
	public static function createSeat(Row $row) {
		$database = Database::open(Settings::db_name_infected_tickets);

		// Find out what seat number we are at.
		$highestSeatNum = $database->query('SELECT `number` FROM `' . Settings::db_table_infected_tickets_seats . '`
											WHERE `rowId` = ' . $row->getId() . '
											ORDER BY `number` DESC
											LIMIT 1;');

		$seatRow = $database->fetch_array($highestSeatNum);

		$newSeatNumber = $seatRow['number'] + 1;

		$database->query('INSERT INTO `' . Settings::db_table_infected_tickets_seats . '` (`rowId`, `number`)
						  VALUES (\'' . $row->getId() . '\',
								  \'' . $database->real_escape_string($newSeatNumber) . '\');');

		$database->close();
	}

	/*
	 * Removes the specified seat.
	 */
	public static function removeSeat(Seat $seat) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('DELETE FROM `' . Settings::db_table_infected_tickets_seats . '`
									WHERE `id` = ' . $seat->getId() . ';');

		$database->close();
	}

	/*
	 * Returns true if this seat has a ticket seated on it.
	 */
	public static function hasTicket(Seat $seat) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '`
									WHERE `seatId` = ' . $seat->getId() . ';');

		$row = $result->fetch_array();

		return $result->num_rows > 0;
	}

	/*
	 * Returns the ticket that is seated on this seat.
	 */
	public static function getTicket(Seat $seat) {
		$database = Database::open(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `seatId` = ' . $seat->getId() . ';');

		$database->close();

		return $result->fetch_object('Ticket');
	}

	/*
	 * Returns the event this seat is for.
	 */
	public static function getEvent(Seat $seat) {
		return RowHandler::getEvent($seat->getRow());
	}
}
?>
