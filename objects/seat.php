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

require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';
require_once 'objects/databaseobject.php';

class Seat extends DatabaseObject {
	private $rowId;
	private $number;

	/*
	 * Returns row this seat belongs to.
	 */
	public function getRow(): Row {
		return RowHandler::getRow($this->rowId);
	}

	/*
	 * Returns seat number relative to row.
	 */
	public function getNumber(): int {
		return $this->number;
	}

	/*
	 * Returns true if there is a ticket that is seated on this seat.
	 */
	public function hasTicket(): bool {
		return SeatHandler::hasTicket($this);
	}

	/*
	 * Returns the ticket that is seated on this seat.
	 */
	public function getTicket(): Ticket {
		return SeatHandler::getTicket($this);
	}

	/*
	 * Returns the event accosiated with this seat.
	 */
	public function getEvent(): Event {
		return SeatHandler::getEvent($this);
	}

	/*
	 * Returns a string representation of this seat.
	 */
	public function getString(): string {
		return 'R' . $this->getRow()->getNumber() . ' S' . $this->getNumber();
	}
}
?>
