<?php
require_once 'handlers/tickethandler.php';
require_once 'location.php';

class Event {
	private $id;
	private $theme;
	private $start;
	private $end;
	private $location;
	private $participants;
	private $seatmap;
	private $ticketType;
	
	public function __construct($id, $theme, $start, $end, $location, $participants, $seatmap, $ticketType) {
		$this->id = $id;
		$this->theme = $theme;
		$this->start = $start;
		$this->end = $end;
		$this->location = $location;
		$this->participants = $participants;
		$this->seatmap = $seatmap;
		$this->ticketType = $ticketType;
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

	public function getSeatmap() {
		return $this->seatmap;
	}
	
	public function getTicketCount() {
		return TicketHandler::getAvailableTicketsForEvent($this);
	}

	public function getTicketType() {
		return $this->ticketType;
	}
}
?>
