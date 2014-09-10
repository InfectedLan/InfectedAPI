<?php
class Clan {
	private $id;
	private $chief;
	private $name;
	private $event;

	public function __construct($id, $chief, $name, $event) {
		$this->id = $id;
		$this->chief = $chief;
		$this->name = $name;
		$this->event = $event;
	}

	public function getId() {
		return $this->id;
	}

	public function getChief() {
		return $this->chief;
	}

	public function getName() {
		return $this->name;
	}

	public function getEvent() {
		return $this->event;
	}
}
?>