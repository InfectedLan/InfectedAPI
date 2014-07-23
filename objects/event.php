<?php
require_once 'location.php';

class Event {
	private $id;
	private $theme;
	private $start;
	private $end;
	private $location;
	private $participants;
	private $price;
	
	public function Event($id, $theme, $start, $end, $location, $participants, $price) {
		$this->id = $id;
		$this->theme = $theme;
		$this->start = $start;
		$this->end = $end;
		$this->location = $location;
		$this->participants = $participants;
		$this->price = $price;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getTheme() {
		return $this->theme;
	}
	
	public function getStartTime() {
		return strtotime($this->start);
	}
	
	public function getEndTime() {
		return strtotime($this->end);
	}

	public function getLocation() {
		return $this->location;
	}

	public function getParticipants() {
		return $this->participants;
	}
	
	public function getPrice() {
		return $this->price;
	}
}
?>
