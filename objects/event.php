<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'handlers/locationhandler.php';
require_once 'handlers/seatmaphandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'handlers/tickethandler.php';
require_once 'handlers/storesessionhandler.php';
require_once 'objects/object.php';
require_once 'objects/location.php';

class Event extends Object {
	private $theme;
	private $locationId;
	private $participants;
	private $bookingTime;
	private $prioritySeatingTime;
	private $seatingTime;
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
	 * Returns the time when priority seating starts(See settings.php)
	 */
	public function getPrioritySeatingTime() {
		return strtotime($this->prioritySeatingTime);
	}

	/*
	 * Returns the time when normal seating starts
	 */
	public function getSeatingTime() {
		return strtotime($this->seatingTime);
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
		return Settings::name . ' ' . (date('m', $this->getStartTime()) == 2 ? 'Vinter' : 'Høst') . ' ' . date('Y', $this->getStartTime()); // TODO: Add localization for this.
	}

	/*
	 * Returns true if booking for this event is opened.
	 */
	public function isBookingTime() {
		$offset = 24 * 60 * 60;
		$bookingTime = $this->getBookingTime();
		$bookingEndTime = $this->getStartTime() + $offset;

		return time() >= $bookingTime && time() <= $bookingEndTime;
	}

	/*
	 * Returns true if booking for this event is opened.
	 */
	public function isOngoing() {
		$offset = 2 * 60 * 60;
		$startTime = $this->getStartTime() - $offset;
		$endTime = $this->getEndTime() + $offset;

		return time() >= $startTime && time() <= $endTime;
	}

	/*
	 * Returns the number of tickets for this event.
	 */
	public function getTicketCount() {
		return count(TicketHandler::getTickets($this));
	}

	/*
	 * Returns the number of tickets left for this event.
	 */
	public function getAvailableTickets() {
		$ticketCount = $this->getTicketCount();
		$ticketsLeft = $this->getParticipants() - $ticketCount;
		$ticketsLeft -= StoreSessionHandler::getReservedTicketCount($this->getTicketType());

		return max(0, $ticketsLeft);
	}
}
?>
