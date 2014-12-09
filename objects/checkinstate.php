<?php
class CheckinState {
	private $id;
	private $ticketId;

	public function __construct($id, $ticketId) {
		$this->id = $id;
		$this->ticketId = $ticketId;
	}

	public function getId() {
		return $this->id;
	}

	public function getTicketId() {
		return $this->ticketId;
	}
}
?>