<?php
require_once 'handlers/entrancehandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'objects/object.php';

class Row extends Object {
	private $number;
	private $x;
	private $y;
	private $entranceId;
	private $seatmapId;

	/*
	 * Returns the row of the section.
	 */
	public function getNumber() {
		return $this->number;
	}
	
	/*
	 * Returns the x coordinate of the section.
	 */
	public function getX() {
		return $this->x;
	}

	/*
	 * Returns the y coordinate of the section.
	 */
	public function getY() {
		return $this->y;
	}

	/*
	 * Returns the row of the section.
	 */
	public function getEntrance() {
		return EntranceHandler::getEntrance($this->entranceId);
	}

	/*
	 * Returns the seatmap the section belongs to.
	 */
	public function getSeatmap() {
		return SeatmapHandler::getSeatmap($this->seatmapId);
	}

	/*
	 * Returns the event accosiated with this seat.
	 */
	public function getEvent() {
		return RowHandler::getEvent($this);
	}

	/*
	 * Returns a list of all the seats on this row.
	 */
	public function getSeats() {
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
}
?>