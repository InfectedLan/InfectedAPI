<?php
require_once 'handlers/rowhandler.php';

class Seat {
	private $id;
	private $rowId;
	private $number;

	/*
	 * Seat - represents a seat
	 * 
	 * Id: Unique id of seat
	 * Section: Section object this seat belongs to
	 * Number: Number relative to row, this seat is at, relative to the row the seat is a part of
	 */
	public function __construct($id, $rowId, $number) {
		$this->id = $id;
		$this->rowId = $rowId;
		$this->number = $number;
	}

	/*
	 * Returns unique id of this seat
	 */
	public function getId() {
		return $this->id;
	}

	/*
	 * Returns row this seat belongs to
	 */
	public function getRow() {
		return RowHandler::getRow($this->rowId);
	}

	/*
	 * Returns seat number relative to row
	 */
	public function getNumber() {
		return $this->number;
	}
}
?>