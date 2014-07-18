<?php
class Seat {
	private $id;
	private $row;
	private $number;

	/*
	 * Seat - represents a seat
	 * 
	 * Id: Unique id of seat
	 * Section: Section object this seat belongs to
	 * Number: Number relative to row, this seat is at, relative to the row the seat is a part of
	 */
	public function Seat($id, $row, $number)
	{
		$this->id = $id;
		$this->row = $row;
	}

	/*
	 * Returns unique id of this seat
	 */
	public function getId()
	{
		return $this->id;
	}

	/*
	 * Returns row this seat belongs to
	 */
	public function getRow()
	{
		return $this->row;
	}

	/*
	 * Returns seat number relative to row
	 */
	public function getNumber()
	{
		return $this->number;
	}

}
?>