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
	
	/*
	 * Returns the description for this agenda.
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/*
	 * Returns the startTime of this agenda.
	 */
	public function getStartTime() {
		return strtotime($this->startTime);
	}
	
	/*
	 * Returns true if this agenda is published.
	 */
	public function isPublished() {
		return $this->published ? true : false;
	}
	
	/*
	 * Returns true if this agenda is happening right now.
	 */
	public function isHappening() {
		return $this->getStartTime() - 5 * 60 >= time() || 
			   $this->getStartTime() + 1 * 60 * 60 >= time();
	}
}
?>