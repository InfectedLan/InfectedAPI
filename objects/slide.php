<?php
require_once 'handlers/eventhandler.php';
require_once 'objects/object.php';

class Slide extends EventObject {
	private $name;
	private $title;
	private $content;
	private $startTime;
	private $endTime;
	private $published;
	
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