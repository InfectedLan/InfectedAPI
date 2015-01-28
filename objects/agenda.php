<?php
require_once 'handlers/eventhandler.php';
require_once 'objects/object.php';

class Agenda extends Object {
	private $eventId;
	private $name;
	private $title;
	private $description;
	private $startTime;
	
	public function __construct($id, $eventId, $name, $title, $description, $startTime) {
		parent::__construct($id);

		$this->eventId = $eventId;
		$this->name = $name;
		$this->title = $title;
		$this->description = $description;
		$this->startTime = $startTime;
	}
	
	public function getEvent() {
		return EventHandler::getEvent($this->eventId);
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getStartTime() {
		return strtotime($this->startTime);
	}
	
	public function isHappening() {
		return $this->getStart() - 5 * 60 >= time() || $this->getStart() + 1 * 60 * 60 >= time();
	}
}
?>