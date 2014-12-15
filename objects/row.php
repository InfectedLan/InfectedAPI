<?php
require_once 'handlers/entrancehandler.php';
require_once 'objects/object.php';

class Row extends Object {
	private $number;
	private $x;
	private $y;
	private $entrance;
	private $seatmap;

	/*
	 * Row - implementation of a section of seats
	 *
	 * Id: Unique id of section
	 * Number: Row number
	 * X: X coordinate of section
	 * Y: Y coordinate of section
	 * Entrance: Entrance number
	 * Event: Event this section belongs to
	 */ 
	public function __construct($id, $number, $x, $y, $entrance, $seatmap) {
		parent::__construct($id);
		
		$this->number = $number;
		$this->x = $x;
		$this->y = $y;
		$this->entrance = $entrance;
		$this->seatmap = $seatmap;
	}

	/*
	 * Returns the row of the section
	 */
	public function getNumber() {
		return $this->number;
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
	public function getEntrance() {
		return EntranceHandler::getEntrance($this->entrance);
	}

	/*
	 * Returns the seatmap the section belongs to
	 */
	public function getSeatmap() {
		return $this->seatmap;
	}
}
?>