<?php
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
        return 'R' . $this->getRow()->getNumber() . ' S' . $this->getNumber();
    }
}
?>