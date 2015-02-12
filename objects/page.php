<?php
require_once 'objects/object.php';

class Page extends Object {
	protected $name;
	protected $title;
	protected $content;
	
	public function getName() {
		return $this->name;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getContent() {
		return $this->content;
	}
}
?>