<?php
require_once 'handlers/locationhandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'handlers/storesessionhandler.php';
require_once 'location.php';

class Event {
	private $id;
	private $theme;
	private $location;
	private $participants;
	private $bookingTime;
	private $startTime;
	private $endTime;
	private $seatmap;
	private $ticketType;
	
	public function __construct($id, $theme, $location, $participants, $bookingTime, $startTime, $endTime, $seatmap, $ticketType) {
		$this->id = $id;
		$this->theme = $theme;
		$this->location = $location;
		$this->participants = $participants;
		$this->bookingTime = $bookingTime;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->seatmap = $seatmap;
		$this->ticketType = $ticketType;
	}
	
	/*
	 * Returns the id.
	 */
	public function getId() {
		return $this->id;
	}
	
	/*
	 * Returns theme of this event.
	 */
	public function getTheme() {
		return $this->theme;
	}
	
	/*
	 * Returns the event location.
	 */
	public function getLocation() {
		return LocationHandler::getLocation($this->location);
	}

	/*
	 * Returns the number of paricipants for this event.
	 */
	public function getParticipants() {
		return $this->participants;
	}
	
	/*
	 * Returns the time when the booking starts.
	 */
	public function getBookingTime() {
		return strtotime($this->bookingTime);
	}
	
	/*
	 * Returns when the event starts.
	 */
	public function getStartTime() {
		return strtotime($this->startTime);
	}
	
	/*
	 * Returns when the event ends.
	 */
	public function getEndTime() {
		return strtotime($this->endTime);
	}

	public function getSeatmap() {
		return SeatmapHandler::getSeatmap($this->seatmap);
	}

	public function getTicketType() {
		return TicketTypeHandler::getTicketType($this->ticketType);
	}
	
	/*
	 * Returns true if booking for this event is open.
	 */
	public function isBookingTime() {
		$bookingTime = $this->getBookingTime();
		$bookingEndTime = $this->getStartTime() + 86400;
		$now = strtotime(date('Y-m-d H:i:s'));

		return $now >= $bookingTime && $now <= $bookingEndTime;
	}
	
	public function getTicketCount() {
		return TicketHandler::getTicketCount($this);
	}

	public function getAvailableTickets() {
		$ticketCount = $this->getTicketCount();
		$numLeft = $this->getParticipants() - $ticketCount;
		$numLeft -= StoreSessionHandler::getReservedTicketCount(TicketTypeHandler::getTicketType($this->ticketType));
		
		return $numLeft;
	}
}
?>
