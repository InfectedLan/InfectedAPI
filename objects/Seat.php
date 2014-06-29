<?php
class Seat {
	private $id;
	private $section;
	private $number;

	/*
	 * Seat - represents a seat
	 * 
	 * Id: Unique id of seat
	 * Section: Section object this seat belongs to
	 * Number: Number relative to section, this seat is at, relative to the section the seat is a part of
	 */
	public function Seat($id, $section, $number)
	{
		$this->id = $id;
		$this->section = $section;
	}

	/*
	 * Returns unique id of this seat
	 */
	public function getId()
	{
		return $this->id;
	}

	/*
	 * Returns section this seat belongs to
	 */
	public function getSection()
	{
		return $this->section;
	}

	/*
	 * Returns seat number relative to section
	 */
	public function getNumber()
	{
		return $this->number;
	}

}
?>