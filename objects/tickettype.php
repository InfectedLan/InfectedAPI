<?php
require_once 'handlers/eventhandler.php';
require_once 'handlers/tickethandler.php';
require_once 'objects/object.php';
require_once 'objects/user.php';

class TicketType extends Object {
	private $name;
	private $title;
	private $price;

	/*
	 * Returns the internal name of this ticket type.
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Returns the name of this ticket type.
	 */
	public function getTitle() {
		return $this->title;
	}

	/*
	 * Returns the price of this ticket type.
	 */
	public function getPrice() {
		return $this->price;
	}

	/*
	 * Returns the price of this ticket, taking discount into consideration
	 */
	public function getPriceByUser(User $user) {
		$price = $this->getPrice();
		$discount = 20;
		$event = EventHandler::getCurrentEvent();
		$ticketList = TicketHandler::getTicketsByUser($user);
		
		foreach ($ticketList as $ticket) {
			$year = date('Y', $event->getStartTime());
			$ticketYear = date('Y', $ticket->getEvent()->getStartTime());
		
			// We'll check if this user has a ticket in the same calender year, if it has, then give the discount.
			if ($year == $ticketYear) {
				if ($event->equals($ticket->getEvent())) {
					$price -= $discount;
				}
			}
		}
		
		return $price;
	}
}
?>