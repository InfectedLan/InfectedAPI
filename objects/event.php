<?php
require_once 'handlers/locationhandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'handlers/storesessionhandler.php';
require_once 'objects/object.php';
require_once 'objects/location.php';

class Event extends Object {
	private $theme;
	private $location;
	private $participants;
	private $bookingTime;
	private $startTime;
	private $endTime;
	private $seatmap;
	private $ticketType;
	
	public function __construct($id, $theme, $location, $participants, $bookingTime, $startTime, $endTime, $seatmap, $ticketType) {
		parent::__construct($id);
	
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

	/*
	 * Returns the seatmap for this event.
	 */
	public function getSeatmap() {
		return SeatmapHandler::getSeatmap($this->seatmap);
	}

	/*
	 * Returns the ticket type for this event.
	 */
	public function getTicketType() {
		return TicketTypeHandler::getTicketType($this->ticketType);
	}
	
	/*
	 * Returns the title for this event.
	 */
	public function getTitle() {
		return 'Infected ' . (date('m', $event->getStartTime()) == 2 ? 'Vinter' : 'Høst') . ' ' . date('Y', $event->getStartTime()
	}
	
	/*
	 * Returns true if booking for this event is opened.
	 */
	public function isBookingTime() {
		$offset = 86400;
		$bookingTime = $this->getBookingTime();
		$bookingEndTime = $this->getStartTime() + $offset;

		return time() >= $bookingTime && time() <= $bookingEndTime;
	}
	
	/*
	 * Returns the number of tickets for this event.
	 */
	public function getTicketCount() {
		return TicketHandler::getTicketCount($this);
	}
	
	/*
	 * Returns the number of tickets left for this event.
	 */
	public function getAvailableTickets() {
		$ticketCount = $this->getTicketCount();
		$numLeft = $this->getParticipants() - $ticketCount;
		$numLeft -= StoreSessionHandler::getReservedTicketCount(TicketTypeHandler::getTicketType($this->ticketType));
		
		return $numLeft;
	}
}
?>
