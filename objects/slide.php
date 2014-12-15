<?php
require_once 'objects/object.php';

class Slide extends Object {
	private $start;
	private $end;
	private $title;
	private $content;
	private $published;
	
	public function __construct($id, $start, $end, $title, $content, $published) {
		parent::__construct($id);
		
		$this->start = $start;
		$this->end = $end;
		$this->title = $title;
		$this->content = $content;
		$this->published = $published;
	}
	
	public function getStart() {
		return strtotime($this->start);
	}
	
	public function getEnd() {
		return strtotime($this->end);
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function isPublished() {
		return $this->published ? true : false;
	}
}
?>