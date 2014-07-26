<?php
class TicketType {
	private $id;
	private $humanName;

	/*
	 * Ticket type
	 *
	 * Ticket type implementation
	 *
	 * Id: Unique id of ticket type
	 * HumanName: Human readable name for tickets
	 */
	public function __construct($id, $humanName) {
		$this->id = $id;
		$this->humanName = $humanName;
	}

	public function getId() {
		return $this->id;
	}

	public function getHumanName() {
		return $this->humanName;
	}
}
?>