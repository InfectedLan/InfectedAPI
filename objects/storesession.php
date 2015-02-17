<?php
require_once 'handlers/userhandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'objects/object.php';

class StoreSession extends Object {
	private $userId;
	private $ticketType;
	private $amount;
	private $code;
	private $price;
	private $datetime;

	/*
	 * Returns the user connected to this session.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the ticket type the user is buying.
	 */
	public function getTicketType() {
		return TicketTypeHandler::getTicketType($this->ticketType);
	}

	/*
	 * Returns the amount of tickets the user is buying.
	 */
	public function getAmount() {
		return $this->amount;
	}

	/*
	 * Returns the key used during purchasing.
	 */
	public function getCode() {
		return $this->code;
	}

	/*
	 * Returns the price the user was supposed to pay.
	 */
	public function getPrice() {
		return $this->price;
	}
	
	/*
	 * Returns the time this session was created.
	 */
	public function getTimeCreated() {
		return strtotime($this->datetime);
	}
}
?>