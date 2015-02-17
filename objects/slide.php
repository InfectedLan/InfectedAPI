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
	
	/*
	 * Returns the name of this slide.
	 */
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the title of this slide.
	 */
	public function getTitle() {
		return $this->title;
	}	
	
	/*
	 * Returns the content of this slide.
	 */
	public function getContent() {
		return $this->content;
	}
	
	/*
	 * Returns the start time of this slide.
	 */
	public function getStartTime() {
		return strtotime($this->startTime);
	}
	
	/*
	 * Returns the end time of this slide.
	 */
	public function getEndTime() {
		return strtotime($this->endTime);
	}
	
	/*
	 * Returns true if this slide is published.
	 */
	public function isPublished() {
		return $this->published ? true : false;
	}
}
?>