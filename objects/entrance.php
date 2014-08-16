<?php
class Entrance {
	private $id;
	private $name;
	private $title;
	
	public function __construct($id, $name, $title) {
		$this->id = $id;
		$this->name = $name;
		$this->title = $title;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTitle() {
		return $this->title;
	}
}
?>