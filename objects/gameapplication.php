<?php
require_once 'handlers/eventhandler.php';
require_once 'handlers/gamehandler.php';

class GameApplication {
	private $id;
	private $eventId;
	private $gameId;
	private $name;
	private $tag;
	private $contactname;
	private $contactnick;
	private $email;
	private $phone;
	
	public function __construct($id, $eventId, $gameId, $name, $tag, $contactname, $contactnick, $phone, $email) {
		$this->id = $id;
		$this->eventId = $eventId;
		$this->gameId = $gameId;
		$this->name = $name;
		$this->tag = $tag;
		$this->contactname = $contactname;
		$this->contactnick = $contactnick;
		$this->email = $email;
		$this->phone = $phone;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getEvent() {
		return EventHandler::getEvent($this->eventId);
	}
	
	public function getGame() {
		return GameHandler::getGame($this->gameId);
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTag() {
		return $this->tag;
	}	
	
	public function getContactname() {
		return $this->contactname;
	}
	
	public function getContactnick() {	
		return $this->contactnick;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function getPhone() {
		return $this->phone;
	}
}
?>