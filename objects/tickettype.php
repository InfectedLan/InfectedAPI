<?php
class TicketType {
	private $id;
	private $humanName;
	private $price;
	private $internalName;

	/*
	 * Ticket type
	 *
	 * Ticket type implementation
	 *
	 * Id: Unique id of ticket type
	 * HumanName: Human readable name for tickets
	 */
	public function __construct($id, $humanName, $price, $internalName) {
		$this->id = $id;
		$this->humanName = $humanName;
		$this->price = $price;
		$this->internalName = $internalName;
	}

	public function getId() {
		return $this->id;
	}

	public function getHumanName() {
		return $this->humanName;
	}

	public function getPrice() {
		return $this->price;
	}

	public function getInternalName() {
		return $this->internalName;
	}
}
?>