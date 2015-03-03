<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'handlers/rowhandler.php';
require_once 'handlers/seathandler.php';
require_once 'objects/object.php';

class Seat extends Object {
	private $rowId;
	private $number;

	/*
	 * Returns row this seat belongs to.
	 */
	public function getRow() {
		return RowHandler::getRow($this->rowId);
	}

	/*
	 * Returns seat number relative to row.
	 */
	public function getNumber() {
		return $this->number;
	}

	/*
	 * Returns true if there is a ticket that is seated on this seat.
	 */
	public function hasTicket() {
		return SeatHandler::hasTicket($this);
	}

	/*
	 * Returns the ticket that is seated on this seat.
	 */
	public function getTicket() {
		return SeatHandler::getTicket($this);
	}

	/*
	 * Returns the event accosiated with this seat.
	 */
	public function getEvent() {
		return SeatHandler::getEvent($this);
	}

	/*
     * Returns a string representation of this seat.
     */
    public function getString() {
        return 'R' . $this->getRow()->getNumber() . 'S' . $this->getNumber();
    }
}
?>