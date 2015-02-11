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
	
	public function getName() {
		return $this->name;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getPrice() {
		return $this->price;
	}
	
	public function getMode() {
		return $this->mode;
	}

	public function getDescription() {
		return $this->description;
	}
	
	public function getStartTime() {
		return strtotime($this->startTime);
	}
	
	public function getEndTime() {
		return strtotime($this->endTime);
	}
	
	public function isBookingTime() {
		$now = strtotime(date('Y-m-d H:i:s'));
				
		return $now >= $this->getStartTime() && $now <= $this->getEndTime();
	}
	
	public function isPublished() {
		return $this->published ? true : false;
	}	
}
?>