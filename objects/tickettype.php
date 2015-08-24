<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
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
require_once 'objects/object.php';
require_once 'objects/user.php';

class TicketType extends Object {
	private $name;
	private $title;
	private $price;
	private $refundable;

	/*
	 * Returns the internal name of this ticket type.
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Returns the name of this ticket type.
	 */
	public function getTitle() {
		return $this->title;
	}

	/*
	 * Returns the price of this ticket type.
	 */
	public function getPrice() {
		return $this->price;
	}

	/*
	 * Returns the true if this ticket type is refundable.
	 */
	public function isRefundable() {
		return $this->refundable ? true : false;
	}

	/*
	 * Returns the price of this ticket, taking discount into consideration
	 */
	public function getPriceByUser(User $user, $amount = 1) {
		$discount = 20; // Radar event discount, membership goes per calender year.

		$eventYear = date('Y', EventHandler::getCurrentEvent()->getStartTime());
		$fee = $discount; // By default the fee is the same as the discount, this will be added to the total price for this ticket/tickets.

		foreach (TicketHandler::getTicketsByUserAndAllEvents($user) as $ticket) {
			$ticketType = $ticket->getType();
			$ticketYear = date('Y', $ticket->getEvent()->getStartTime());

			// We'll check if this user has a ticket in the same calender year, if it has, then give the discount.
			if ($ticketYear == $eventYear) {
				// Only give discount to tickets that actually have a price greater than 0.
				if ($ticketType->getPrice() > 0) {
					$fee = 0;
				}
			}
		}

		return (($this->getPrice() - $discount) * $amount) + $fee;
	}
}
?>
