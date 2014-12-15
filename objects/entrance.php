<?php
require_once 'objects/object.php';

class Entrance extends Object{
	private $name;
	private $title;
	
	public function __construct($id, $name, $title) {
		parent::__construct($id);

		$this->name = $name;
		$this->title = $title;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTitle() {
		return $this->title;
	}
}
?>