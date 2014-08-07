<?php
require_once 'handlers/eventhandler.php';

class Agenda {
	private $id;
	private $event;
	private $name;
	private $title;
	private $description;
	private $start;
	
	public function __construct($id, $event, $name, $title, $description, $start) {
		$this->id = $id;
		$this->event = $event;
		$this->name = $name;
		$this->title = $title;
		$this->description = $description;
		$this->start = $start;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getEvent() {
		return EventHandler::getEvent($this->event);
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
	
	public function getStart() {
		return strtotime($this->start);
	}
	
	public function isHappening() {
		$now = date('U');
			
		if ($this->getStart() - 5 * 60 >= $now ||
			$this->getStart() + 1 * 60 * 60 >= $now) {
			return true;
		}
		
		return false;
	}
}
?>