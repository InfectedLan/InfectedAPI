<?php
require_once 'handlers/userhandler.php';
require_once 'handlers/tickettypehandler.php';
require_once 'objects/object.php';

class Payment {
	private $userId;
	private $ticketType;
	private $price;
	private $totalPrice;
	private $transactionId;
	private $datetime;

	public function __construct($id, $userId, $ticketType, $price, $totalPrice, $transactionId, $datetime) {
		parent::__construct($id);
		
		$this->userId = $userId;
		$this->ticketType = $ticketType;
		$this->price = $price;
		$this->totalPrice = $totalPrice;
		$this->transactionId = $transactionId;
		$this->datetime = $datetime;
	}

	public function getUser() {
		return UserHandler::getUser($this->userId);
	}

	public function getTicketType() {
		return TicketTypeHandler::getTicketType($this->ticketType);
	}

	public function getPrice() {
		return $this->price;
	}

	public function getTotalPrice() {
		return $this->totalPrice;
	}

	public function getTransactionId() {
		return $this->transactionId;
	}
	
	public function getDateTime() {
		return strtotime($this->datetime);
	}
}
?>