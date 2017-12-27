<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'handlers/tickethandler.php';
require_once 'handlers/rowhandler.php';
require_once 'objects/seat.php';

class SeatHandler {
	/*
	 * Get a seat by the internal id.
	 */
	public static function getSeat(int $id): ?Seat {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Seat');
	}

	/*
	 * Returns a list of all seats.
	 */
	public static function getSeats(): array {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '`;');

		$seatList = [];

		while ($object = $result->fetch_object('Seat')) {
			$seatList[] = $object;
		}

		return $seatList;
	}

	/*
	 * Return all seats on the specified row.
	 */
	public static function getSeatsByRow(Row $row): array {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_seats . '`
																WHERE `rowId` = \'' . $row->getId() . '\';');

		$seatList = [];

		while ($object = $result->fetch_object('Seat')) {
			$seatList[] = $object;
		}

		return $seatList;
	}

	/*
	 * Add a seat to the specified row.
	 */
	public static function createSeat(Row $row, int $number = null): Seat {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		if ($number == null) {
			// Find out what seat number we are at.
			$result = $database->query('SELECT `number` FROM `' . Settings::db_table_infected_tickets_seats . '`
									   WHERE `rowId` = \'' . $row->getId() . '\'
									   ORDER BY `number` DESC
									   LIMIT 1;');

			$seatRow = $result->fetch_array();
			$number = $seatRow['number'] + 1;
		}

		$database->query('INSERT INTO `' . Settings::db_table_infected_tickets_seats . '` (`rowId`, `number`)
						 VALUES (\'' . $row->getId() . '\',
						 		 \'' . $database->real_escape_string($number) . '\');');

		return self::getSeat($database->insert_id);
	}

	/*
	 * Removes the specified seat.
	 */
	public static function removeSeat(Seat $seat) {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('DELETE FROM `' . Settings::db_table_infected_tickets_seats . '`
								   WHERE `id` = \'' . $seat->getId() . '\';');
	}

	/*
	 * Returns true if this seat has a ticket seated on it.
	 */
	public static function hasTicket(Seat $seat): bool {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `seatId` = \'' . $seat->getId() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns the ticket that is seated on this seat.
	 */
	public static function getTicket(Seat $seat): ?Ticket {
		$database = Database::getConnection(Settings::db_name_infected_tickets);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickets . '`
																WHERE `seatId` = \'' . $seat->getId() . '\';');

		return $result->fetch_object('Ticket');
	}

	/*
	 * Returns the event this seat is for.
	 */
	public static function getEvent(Seat $seat): ?Event {
		return RowHandler::getEvent($seat->getRow());
	}

  /*
   * Returns true if the user can seat at the specified seat during priority seating
   */
  public static function canBeSeated(Seat $seat, User $user): bool {
    $seatRow = $seat->getRow();
    $seatableTickets = TicketHandler::getTicketsSeatableByUser($user);
		$seatedCount = 0;

    foreach ($seatableTickets as $ticket) {
      $ticketSeat = $ticket->getSeat();

			if ($ticketSeat==null) { //Ticket is not seated
          continue;
      }

			$seatedCount++;

			if ($ticketSeat->getRow()->equals($seatRow)) {
        if ($seat->getNumber() - 1 == $ticketSeat->getNumber() || $seat->getNumber() + 1 == $ticketSeat->getNumber()) {
          return true;
        }
      }
    }

		return $seatedCount == 0;
  }
}
?>
