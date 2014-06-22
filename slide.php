<?php
class Slide {
	private $id;
	private $start;
	private $end;
	private $title;
	private $content;
	private $published;
	
	public function Slide($id, $start, $end, $title, $content, $published) {
		$this->id = $id;
		$this->start = $start;
		$this->end = $end;
		$this->title = $title;
		$this->content = $content;
		$this->published = $published;
	}
	
	public function getId() {
		return $this->id;
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