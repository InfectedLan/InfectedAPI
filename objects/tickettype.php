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

require_once 'handlers/eventhandler.php';
require_once 'handlers/tickethandler.php';
require_once 'objects/databaseobject.php';
require_once 'objects/user.php';

class TicketType extends DatabaseObject {
	private $name;
	private $title;
	private $price;
	private $refundable;

	/*
	 * Returns the internal name of this ticket type.
	 */
	public function getName(): string {
		return $this->name;
	}

	/*
	 * Returns the name of this ticket type.
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/*
	 * Returns the price of this ticket type.
	 */
	public function getPrice(): int {
		return $this->price;
	}

	/*
	 * Returns the true if this ticket type is refundable.
	 */
	public function isRefundable(): bool {
		return $this->refundable ? true : false;
	}

	public function isUserEligibleForDiscount(User $user): bool {
		$eventYear = date('Y', EventHandler::getCurrentEvent()->getStartTime());

		foreach (TicketHandler::getTicketsByUserAndAllEvents($user) as $ticket) {
			$ticketType = $ticket->getType();
			$ticketYear = date('Y', $ticket->getEvent()->getStartTime());

			// We'll check if this user has a ticket in the same calender year, if it has, then give the discount.
			if ($ticketYear == $eventYear) {
				// Only give discount to tickets that actually have a price greater than 0.
				if ($ticketType->getPrice() > 0) {
					return true;
				}
			}
		}

		return false;
	}

	/*
	 * Returns the price of this ticket, taking discount into consideration
	 */
	public function getPriceByUser(User $user, int $amount = 1): int {
    // A better formula would be (ticketFee*amount)+radarMembership, but then we need to store ticket prices without the membership included.
		// This will propabily confuse some people. Let's keep it this way :)
		$discount = Settings::ticketFee; // Radar event discount, membership goes per calender year.
    $fee = $this->isUserEligibleForDiscount($user) ? 0 : $discount;

		return (($this->getPrice() - $discount) * $amount) + $fee;
	}
}