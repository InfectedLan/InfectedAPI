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
require_once 'objects/databaseobject.php';
require_once 'objects/location.php';

class Event extends DatabaseObject {
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
	public function getTheme(): ?string {
		return $this->theme;
	}

	/*
	 * Returns the event location.
	 */
	public function getLocation(): ?Location {
		return LocationHandler::getLocation($this->locationId);
	}

	/*
	 * Returns the number of paricipants for this event.
	 */
	public function getParticipants(): int {
		return $this->participants;
	}

	/*
	 * Returns the time when the booking starts.
	 */
	public function getBookingTime(): int {
		return strtotime($this->bookingTime);
	}

	/*
	 * Returns the time when priority seating starts(See settings.php)
	 */
	public function getPrioritySeatingTime(): int {
		return strtotime($this->prioritySeatingTime);
	}

	/*
	 * Returns the time when normal seating starts
	 */
	public function getSeatingTime(): int {
		return strtotime($this->seatingTime);
	}

	/*
	 * Returns when the event starts.
	 */
	public function getStartTime(): int {
		return strtotime($this->startTime);
	}

	/*
	 * Returns when the event ends.
	 */
	public function getEndTime(): int {
		return strtotime($this->endTime);
	}

	/*
	 * Returns the seatmap for this event.
	 */
	public function getSeatmap(): ?Seatmap {
		return SeatmapHandler::getSeatmap($this->seatmapId);
	}

	/*
	 * Returns the ticket type for this event.
	 */
	public function getTicketType(): ?TicketType {
		return TicketTypeHandler::getTicketType($this->ticketTypeId);
	}

	/*
	 * Returns the title for this event.
	 */
	public function getTitle(): string {
		return Settings::getValue("name") . ' ' . $this->getSeason() . ' ' . date('Y', $this->getStartTime());
	}

	/*
	 * Returns true if booking for this event is opened.
	 */
	public function isBookingTime(): bool {
		return time() >= $this->getBookingTime() && time() <= $this->getEndTime();
	}

	/*
	 * Returns true if booking for this event is opened.
	 */
	public function isOngoing(): bool {
		$offset = 60 * 60;
		$startTime = $this->getStartTime() - $offset;
		$endTime = $this->getEndTime() + $offset;

		return time() >= $startTime && time() <= $endTime;
	}

	/*
	 * Returns this events season.
	 */
	public function getSeason(): string {
		return Localization::getLocale(date('m', $this->getStartTime()) == 2 ? 'winter' : 'autumn');
	}

	/*
	 * Returns the number of tickets for this event.
	 */
	public function getTicketCount(): int {
		return count(TicketHandler::getTickets($this));
	}

	/*
	 * Returns the number of tickets left for this event.
	 */
	public function getAvailableTickets(): int {
		$ticketCount = $this->getTicketCount();
		$ticketsLeft = $this->getParticipants() - $ticketCount;
		$ticketsLeft -= StoreSessionHandler::getReservedTicketCount($this->getTicketType());

		return max(0, $ticketsLeft);
	}
}