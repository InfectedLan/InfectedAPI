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
	
	public function __construct($id, $name, $title, $price, $mode, $description, $startTime, $endTime, $published) {
		parent::__construct($id);
	
		$this->name = $name;
		$this->title = $title;
		$this->price = $price;
		$this->mode = $mode;
		$this->description = $description;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->published = $published;
	}
	
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