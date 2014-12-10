<?php
class CheckinState {
	private $id;
	private $ticketId;
	private $userId;
	
	public function __construct($id, $ticketId, $userId) {
		$this->id = $id;
		$this->ticketId = $ticketId;
		$this->userId = $userId;
	}

	public function getId() {
		return $this->id;
	}

	public function getTicketId() {
		return $this->ticketId;
	}
	
	/*
	 * Returns the user who checked in with this ticket.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}
}
?>