<?php
require_once 'handlers/eventhandler.php';
require_once 'objects/object.php';

class Slide extends Object {
	private $eventId;
	private $name;
	private $title;
	private $content;
	private $startTime;
	private $endTime;
	private $published;
	
	public function __construct($id, $eventId, $name, $title, $content, $startTime, $endTime, $published) {
		parent::__construct($id);

		$this->eventId = $eventId;
		$this->name = $name;
		$this->title = $title;
		$this->content = $content;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->published = $published;
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
	
	public function getContent() {
		return $this->content;
	}
	
	public function getStartTime() {
		return strtotime($this->startTime);
	}
	
	public function getEndTime() {
		return strtotime($this->endTime);
	}
	
	public function isPublished() {
		return $this->published ? true : false;
	}
}
?>