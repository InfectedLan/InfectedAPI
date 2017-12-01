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

require_once 'settings.php';
require_once 'qr.php';
require_once 'handlers/tickettypehandler.php';
require_once 'handlers/paymenthandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/seathandler.php';
require_once 'handlers/tickettransferhandler.php';
require_once 'objects/eventobject.php';
require_once 'objects/user.php';

class Ticket extends EventObject {
	private $typeId;
	private $buyerId;
	private $paymentId;
	private $userId;
	private $seaterId;
	private $seatId;

	/*
	 * Returns the ticket type.
	 */
	public function getType(): TicketType {
		return TicketTypeHandler::getTicketType($this->typeId);
	}

	/*
	 * Returns the buyer of this ticket, also who bought/got it in the first place.
	 */
	public function getBuyer(): User {
		return $this->buyerId != 0 ? UserHandler::getUser($this->buyerId) : $this->getUser();
	}

	/*
	 * Returns the payment that this ticket is linked to, if any.
	 */
	public function getPayment(): Payment {
		return PaymentHandler::getPayment($this->paymentId);
	}

	/*
	 * Returns the user of this ticket.
	 */
	public function getUser(): User {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the seater of this ticket.
	 *
	 * The seater is the user account that is allowed to decide what seat this ticket is seated on.
	 */
	public function getSeater(): User {
		return $this->seaterId > 0 ? UserHandler::getUser($this->seaterId) : $this->getUser();
	}

	/*
	 * Returns the seat that this ticket is seated at.
	 */
	public function getSeat(): Seat {
		return SeatHandler::getSeat($this->seatId);
	}

	/*
	 * Returns a string representation of the ticket.
	 */
	public function toString(): string {
		$event = $this->getEvent();
		$eventName = !empty($event->getTheme()) ? $event->getTheme() : $event->getSeason();

		return strtoupper(Settings::name . '_' . $eventName . '_' . date('Y', $event->getStartTime()) . '_' . $this->getId());
	}

	// TODO: Implement this in a more generic way?
	public function getQrImagePath(): string {
		return QR::getCode('https://infected.no/api/pages/utils/verifyTicket.php?id=' . $this->getId());
	}

	/*
	 * Returns true if this ticket is seated.
	 */
	public function isSeated(): bool {
		return $this->seatId > 0;
	}

	/*
	 * Returns true if this ticket is checked in.
	 */
	public function isCheckedIn(): bool {
		return TicketHandler::isTicketCheckedIn($this);
	}

	/*
	 * Returns true if this ticket can be refunded.
	 */
	public function isRefundable(): bool {
		$event = $this->getEvent();
		$timeLeftToEvent = date('U', $event->getStartTime()) - time();

		return $this->getType()->isRefundable() && $timeLeftToEvent >= Settings::refundBeforeEventTime;
	}

	/*
	 * Returns true if given user is allowed to seat this ticket.
	 */
	public function canSeat(User $user): bool {
		return $user->equals($this->getUser()) && $this->getSeater() == null ||
			   $user->equals($this->getSeater());
	}

	/*
	 * Checks in this ticket.
	 */
	public function checkIn() {
		TicketHandler::checkInTicket($this);
	}

	/*
	 * Transfers this ticket to the specified user.
	 */
	public function transfer(User $user) {
		TicketTransferHandler::transfer($this, $user);
	}

	/*
	 * Revert transfer of this ticket and transfer back to the specified user, if it matches the original sender.
	 */
	public function revertTransfer(User $user) {
		TicketTransferHandler::revertTransfer($this, $user);
	}
}
?>
