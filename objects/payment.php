<?php
require_once 'handlers/userhandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'objects/object.php';

class Payment extends Object{
	private $userId;
	private $ticketTypeId;
	private $price;
	private $totalPrice;
	private $transactionId;
	private $datetime;

	/*
	 * Returns this payments user.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the ticket type for this payment.
	 */
	public function getTicketType() {
		return TicketTypeHandler::getTicketType($this->ticketTypeId);
	}

	/*
	 * Returns the price for this payment.
	 */
	public function getPrice() {
		return $this->price;
	}

	/*
	 * Returns the total price for this payment.
	 */
	public function getTotalPrice() {
		return $this->totalPrice;
	}

	/*
	 * Returns the transaction id of this payment.
	 */
	public function getTransactionId() {
		return $this->transactionId;
	}
	
	/*
	 * Returns the datetime of this payment.
	 */
	public function getDateTime() {
		return strtotime($this->datetime);
	}
}
?>