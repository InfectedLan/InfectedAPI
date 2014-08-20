<?php
require_once 'handlers/tickethandler.php';
require_once 'handlers/eventhandler.php';
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

	/*
	 * Returns the price of this ticket, taking discount into consideration
	 */
	public function getPriceForUser($user) {
		$price = $this->price;
		$discount = 20;
		
		// Check if the user have an registred ticket in the database
		if (TicketHandler::hasTicket($user)) {
			$ticket = TicketHandler::getTicketForUser($user);
			
			// We'll check if this user has a ticket for earlier events, if it has, then give the discount.
			if ($ticket->getEvent()->getId() != EventHandler::getCurrentEvent()->getId()) {
				$price += $discount;
			}
		}
		
		return $price;
	}
}
?>