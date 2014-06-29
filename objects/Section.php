<?php
class Section {
	private $id;
	private $seats;
	private $x;
	private $y;
	private $row;
	private $event;

	/*
	 * Section - implementation of a section of seats
	 *
	 * Id: Unique id of section
	 * Seats: Array of seats in the section
	 * X: X coordinate of section
	 * Y: Y coordinate of section
	 * Row: Row of section
	 * Event: Event this section belongs to
	 */ 
	public function Section($id, $seats, $x, $y, $row, $event) {
		$this->id = $id;
		$this->seats = $seats;
		$this->x = $x;
		$this->y = $y;
		$this->row = $row;
		$this->event = $event;
	}

	/*
	 * Returns the unique id of the section
	 */
	public function getId()
	{
		return $this->id;
	}

	/*
	 * Returns the seat array 
	 */
	public function getSeats()
	{
		return $this->seats;
	}

	/*
	 * Returns the x coordinate of the section
	 */
	public function getX()
	{
		return $this->x;
	}

	/*
	 * Returns the y coordinate of the section
	 */
	public function getY()
	{
		return $this->y;
	}

	/*
	 * Returns the row of the section
	 */
	public function getRow()
	{
		return $this->row;
	}

	/*
	 * Returns the event the section belongs to
	 */
	public function getEvent()
	{
		return $this->event;
	}
}
?>