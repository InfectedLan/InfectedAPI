<?php
require_once 'objects/object.php';

class CheckInState extends Object {
	private $ticketId;
	private $userId;

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