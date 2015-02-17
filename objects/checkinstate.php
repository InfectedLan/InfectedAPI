<?php
require_once 'objects/object.php';

class CheckInState extends Object {
	private $ticketId;
	private $userId;

	/*
	 * Returns the ticket that is checked in.
	 */
	public function getTicket() {
		return TicketHandler::getTicket($this->ticketId);
	}
	
	/*
	 * Returns the user who checked in with the ticket.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}
}
?>