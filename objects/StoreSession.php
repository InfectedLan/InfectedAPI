<?php
class StoreSession {
	private $id;
	private $userId;
	private $timeCreated;

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
	public function __construct($id, $userId, $timeCreated) {
		$this->id = $id;
		$this->userId = $userId;
		$this->timeCreated = $timeCreated;
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
}
?>