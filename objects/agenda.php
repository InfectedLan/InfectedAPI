<?php
require_once 'objects/eventobject.php';

class Agenda extends EventObject {
	private $name;
	private $title;
	private $description;
	private $startTime;
	private $published;
	
	/*
	 * Returns the name of this object.
	 */
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the name of this object.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function getStartTime() {
		return strtotime($this->startTime);
	}
	
	public function isPublished() {
		return $this->published ? true : false;
	}
	
	public function isHappening() {
		return $this->getStartTime() - 5 * 60 >= time() || 
			   $this->getStartTime() + 1 * 60 * 60 >= time();
	}
}
?>