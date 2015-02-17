<?php
require_once 'objects/object.php';

class Entrance extends Object {
	private $name;
	private $title;
	
	public function getName() {
		return $this->name;
	}
	
	public function getTitle() {
		return $this->title;
	}
}
?>