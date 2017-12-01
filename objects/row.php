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

require_once 'handlers/entrancehandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'objects/object.php';

class Row extends Object {
	private $seatmapId;
	private $entranceId;
	private $number;
	private $x;
	private $y;
	private $isHorizontal;

	/*
	 * Returns the seatmap the section belongs to.
	 */
	public function getSeatmap(): Seatmap {
		return SeatmapHandler::getSeatmap($this->seatmapId);
	}

	/*
	 * Returns the row of the section.
	 */
	public function getEntrance(): Entrance {
		return EntranceHandler::getEntrance($this->entranceId);
	}

	/*
	 * Returns the row of the section.
	 */
	public function getNumber(): int {
		return $this->number;
	}

	/*
	 * Returns the x coordinate of the section.
	 */
	public function getX(): int {
		return $this->x;
	}

	/*
	 * Returns the y coordinate of the section.
	 */
	public function getY(): int {
		return $this->y;
	}

	/*
	 * Returns the event accosiated with this seat.
	 */
	public function getEvent(): Event {
		return RowHandler::getEvent($this);
	}

	/*
	 * Returns a list of all the seats on this row.
	 */
	public function getSeats(): array {
		return SeatHandler::getSeatsByRow($this);
	}

	/*
	 * Adds a seat on the specified coordinates.
	 */
	public function addSeat() {
		SeatHandler::createSeat($this);
	}

	/*
	 * Removes the specified seat from this row.
	 */
	public function removeSeat() {
		SeatHandler::removeSeat($this);
	}

	/*
	 * Returns true if row is horizontal
	 */
	public function isHorizontal(): bool {
		return $this->isHorizontal == 1;
	}
}
?>
