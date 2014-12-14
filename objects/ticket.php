<?php
require_once 'settings.php';
require_once 'qr.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'handlers/seathandler.php';

class Ticket {
	private $id;
	private $eventId;
	private $typeId;
	private $seatId;
	private $buyerId;
	private $userId;
	private $seaterId;

	/*
	 * Ticket - implementation of backend ticket db.
	 * 
	 * Id: Unique id of ticket
	 * Event Id: Id of event ticket is connected to
	 * Type Id: Ticket type. Object.
	 * Seat Id: Object of seat ticket is seated on
	 * Buyer Id: User that bought the ticket
	 * User Id: User account that will be using the ticket
	 * Seater Id: User account that can seat this ticket
	 */
	public function __construct($id, $eventId, $typeId, $seatId, $buyerId, $userId, $seaterId) {
		$this->id = $id;
		$this->eventId = $eventId;
		$this->typeId = $typeId;
		$this->seatId = $seatId;
		$this->buyerId = $buyerId;
		$this->userId = $userId;
		$this->seaterId = $seaterId;
	}

	/*
	 * Returns the unique id for the ticket
	 */
	public function getId() {
		return $this->id;
	}

	/*
	 * Returns the event this ticket is for
	 */
	public function getEvent() {
		return EventHandler::getEvent($this->eventId);
	}

	/*
	 * Returns the ticket type
	 */
	public function getType() {
		return TicketTypeHandler::getTicketType($this->typeId);
	}

	/*
	 * Returns the seat that this ticket is seated at
	 */
	public function getSeat() {
		return SeatHandler::getSeat($this->seatId);
	}
	
	/*
	 * Returns the buyer of this ticket, also who bought/got it in the first place.
	 */
	public function getBuyer() {
		return UserHandler::getUser($this->buyerId);
	}
	
	/*
	 * Returns the user of this ticket.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the seater of this ticket.
	 *
	 * The seater is the user account that is allowed to decide what seat this ticket is seated on.
	 */
	public function getSeater() {
		return UserHandler::getUser($this->seaterId);
	}

	/*
	 * Returns a human readable representation of the ticket
	 */
	public function getHumanName() {
		$event = $this->getEvent();
		$season = date('m', $event->getStartTime()) == 2 ? 'VINTER' : 'HØST';
		$eventName = !empty($event->getTheme()) ? $event->getTheme() : $season . date('Y', $event->getStartTime());
	
		return strtoupper(Settings::name . '_' . $eventName . '_' . $this->getId());
	}
	
	public function getQrImagePath() {
		return QR::getCode('https://api.infected.no/functions/verifyTicket.php?id=' . $this->getId());
	}

	public function canSeat($user) {
		return ($this->userId == $user->getId() && $this->seaterId == 0) || 
				$this->seaterId == $user->getId();
	}
}
?>