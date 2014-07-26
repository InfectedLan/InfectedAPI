<?php
class Row {
	private $id;
	private $x;
	private $y;
	private $row;
	private $seatmap;

	/*
	 * Row - implementation of a section of seats
	 *
	 * Id: Unique id of section
	 * X: X coordinate of section
	 * Y: Y coordinate of section
	 * Row: Row number
	 * Event: Event this section belongs to
	 */ 
	public function __construct($id, $x, $y, $row, $seatmap) {
		$this->id = $id;
		$this->x = $x;
		$this->y = $y;
		$this->row = $row;
		$this->seatmap = $seatmap;
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
	 * Returns the seatmap the section belongs to
	 */
	public function getSeatmap() {
		return $this->seatmap;
	}
}
?>