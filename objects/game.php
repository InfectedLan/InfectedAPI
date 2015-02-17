<?php
require_once 'objects/object.php';

class Game extends Object {
	private $name;
	private $title;
	private $price;
	private $mode;
	private $description;
	private $startTime;
	private $endTime;
	private $published;
	
	/*
	 * Returns the name of this game.
	 */ 
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the title of this game.
	 */ 
	public function getTitle() {
		return $this->title;
	}
	
	/*
	 * Returns the price for game.
	 */ 
	public function getPrice() {
		return $this->price;
	}
	
	/*
	 * Returns the mode of this game.
	 */ 
	public function getMode() {
		return $this->mode;
	}

	/*
	 * Returns the description of this game.
	 */ 
	public function getDescription() {
		return $this->description;
	}
	
	/*
	 * Returns the startTime of this game.
	 */ 
	public function getStartTime() {
		return strtotime($this->startTime);
	}
	
	/*
	 * Returns the endTime of this game.
	 */ 
	public function getEndTime() {
		return strtotime($this->endTime);
	}
	
	/*
	 * Returns the bookingTime of this game.
	 */ 
	public function isBookingTime() {	
		return time() >= $this->getStartTime() && time() <= $this->getEndTime();
	}
	
	/*
	 * Returns true if this game is published.
	 */ 
	public function isPublished() {
		return $this->published ? true : false;
	}	
}
?>