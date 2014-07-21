<?php
class Row {
	private $id;
	private $x;
	private $y;
	private $row;
	private $event;

	/*
	 * Row - implementation of a section of seats
	 *
	 * Id: Unique id of section
	 * X: X coordinate of section
	 * Y: Y coordinate of section
	 * Row: Row number
	 * Event: Event this section belongs to
	 */ 
	public function Row($id, $x, $y, $row, $event) {
		$this->id = $id;
		$this->x = $x;
		$this->y = $y;
		$this->row = $row;
		$this->event = $event;
	}

	/*
	 * Returns the unique id of the section
	 */
	public function getId() {
		return $this->id;
	}

	/*
	 * Returns the x coordinate of the section
	 */
	public function getX() {
		return $this->x;
	}

	/*
	 * Returns the y coordinate of the section
	 */
	public function getY() {
		return $this->y;
	}

	/*
	 * Returns the row of the section
	 */
	public function getNumber() {
		return $this->row;
	}

	/*
	 * Returns the event the section belongs to
	 */
	public function getEvent() {
		return $this->event;
	}
}
?>