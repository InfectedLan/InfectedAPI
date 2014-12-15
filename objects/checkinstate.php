<?php
require_once 'objects/object.php';

class CheckinState extends Object {
	private $ticketId;
	private $userId;
	
	public function __construct($id, $ticketId, $userId) {
		parent::__construct($id);
		
		$this->ticketId = $ticketId;
		$this->userId = $userId;
	}

	// TODO: Return ticket instead of ticket id here?
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