<?php
require_once 'handlers/rowhandler.php';
require_once 'objects/object.php';

class Seat extends Object {
	private $rowId;
	private $number;

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