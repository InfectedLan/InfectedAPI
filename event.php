<?php
class Event {
	private $id;
	private $theme;
	private $participants;
	private $price;
	private $start;
	private $end;
	
	public function Event($id, $theme, $participants, $price, $start, $end) {
		$this->id = $id;
		$this->theme = $theme;
		$this->participants = $participants;
		$this->price = $price;
		$this->start = $start;
		$this->end = $end;
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
}
?>