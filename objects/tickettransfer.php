<?php
require_once 'handlers/tickethandler.php';
require_once 'handlers/userhandler.php';

class TicketTransfer extends Object{
	private $ticketId;
	private $fromId;
	private $toId;
	private $datetime;
	private $revertable;

	public function __construct($id, $ticketId, $fromId, $toId, $datetime, $revertable) {
		parent::__construct($id);

		$this->ticketId = $ticketId;
		$this->fromId = $fromId;
		$this->toId = $toId;
		$this->datetime = $datetime;
		$this->revertable = $revertable;
	}
	
	public function getTicket() {
		return TicketHandler::getTicket($this->ticketId);
	}
	
	/*
	 * Returns the sender user.
	 */
	public function getFrom() {
		return UserHandler::getUser($this->fromId);
	}
	
	/*
	 * Returns the receiving user.
	 */
	public function getTo() {
		return UserHandler::getUser($this->toId);
	}

	public function getDateTime() {
		return strtotime($this->datetime);
	}

	public function isRevertable() {
		return $this->revertable ? true : false;
	}
}
?>