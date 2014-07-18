<?php
class Event {
	private $id;
	private $theme;
	private $participants;
	private $price;
	private $start;
	private $end;
	private $location;
	
	public function Event($id, $theme, $participants, $price, $start, $end, $location) {
		$this->id = $id;
		$this->theme = $theme;
		$this->participants = $participants;
		$this->price = $price;
		$this->start = $start;
		$this->end = $end;
		$this->location = $location;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getTheme() {
		return $this->theme;
	}
	
	public function getParticipants() {
		return $this->participants;
	}
	
	public function getPrice() {
		return $this->price;
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
}
?>