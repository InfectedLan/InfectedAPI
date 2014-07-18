<?php
class Game {
	private $id;
	private $name;
	private $title;
	private $price;
	private $mode;
	private $description;
	private $deadline;
	private $published;
	
	public function Game($id, $name, $title, $price, $mode, $description, $deadline, $published) {
		$this->id = $id;
		$this->name = $name;
		$this->title = $title;
		$this->price = $price;
		$this->mode = $mode;
		$this->description = $description;
		$this->deadline = $deadline;
		$this->published = $published;
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
	
	public function getPrice() {
		return $this->price;
	}
	
	public function getMode() {
		return $this->mode;
	}

	public function getDescription() {
		return $this->description;
	}
	
	public function getDeadline() {
		return strtotime($this->deadline);
	}
	
	public function isPublished() {
		return $this->published ? true : false;
	}	
}
?>