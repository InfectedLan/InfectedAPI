<?php
require_once 'handlers/locationhandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'handlers/storesessionhandler.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/object.php';
require_once 'objects/location.php';

class Event extends Object {
	private $theme;
	private $locationId;
	private $participants;
	private $bookingTime;
	private $startTime;
	private $endTime;
	private $seatmapId;
	private $ticketTypeId;
	
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
		return LocationHandler::getLocation($this->locationId);
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
		return SeatmapHandler::getSeatmap($this->seatmapId);
	}

	/*
	 * Returns the ticket type for this event.
	 */
	public function getTicketType() {
		return TicketTypeHandler::getTicketType($this->ticketTypeId);
	}
	
	/*
	 * Returns the title for this event.
	 */
	public function getTitle() {
		return Settings::name . ' ' . (date('m', $this->getStartTime()) == 2 ? 'Vinter' : 'Høst') . ' ' . date('Y', $this->getStartTime());
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
		return count(TicketHandler::getTicketsByEvent($this));
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

	/*
	 * Clone group members from the specified event to this one.
	 */
	public function cloneMembersFromEvent($event) {
		EventHandler::cloneMembers($event, $this);
	}
}
?>
