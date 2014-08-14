<?php
class StoreSession {
	private $id;
	private $userId;
	private $timeCreated;
	private $ticketType;
	private $amount;
	private $key;

	/*
	 * StoreSession - represents a session in the ticket shop.
	 * 
	 * This is used to reserve a ticket for someone after they pressed "buy", until the payment has been processed.
	 * I didnt name it session because that would be confusing.
	 * 
	 * Id: Unique id of seat
	 * UserId: ID of user connected to the session
	 * TimeCreated: time this session was created, used for calculating if session has timed out
	 */
	public function __construct($id, $userId, $timeCreated, $ticketType, $amount, $key) {
		$this->id = $id;
		$this->userId = $userId;
		$this->timeCreated = $timeCreated;
		$this->ticketType = $ticketType;
		$this->amount = $amount;
		$this->key = $key;
	}

	/*
	 * Returns unique id of this session
	 */
	public function getId() {
		return $this->id;
	}

	/*
	 * Returns the user connected to this session
	 */
	public function getUserId() {
		return $this->userId;
	}

	/*
	 * Returns the time this session was created
	 */
	public function getTimeCreated() {
		return $this->timeCreated;
	}

	/*
	 * Returns the ticket type the user is buying
	 */
	public function getTicketType() {
		return $this->ticketType;
	}

	/*
	 * Returns the amount of tickets the user is buying
	 */
	public function getAmount() {
		return $this->amount;
	}

	/*
	 * Returns the key used during purchasing
	 */
	public function getKey() {
		return $this->key;
	}
}
?>