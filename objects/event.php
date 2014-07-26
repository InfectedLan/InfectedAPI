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
	private $seatmap;
	
	public function __construct($id, $theme, $start, $end, $location, $participants, $price, $seatmap) {
		$this->id = $id;
		$this->theme = $theme;
		$this->start = $start;
		$this->end = $end;
		$this->location = $location;
		$this->participants = $participants;
		$this->price = $price;
		$this->seatmap = $seatmap;
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

	public function getSeatmap() {
		return $this->seatmap;
	}
}
?>
