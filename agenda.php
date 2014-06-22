<?php
class Agenda {
	private $id;
	private $datetime;
	private $name;
	private $description;
	
	public function Agenda($id, $datetime, $name, $description) {
		$this->id = $id;
		$this->datetime = $datetime;
		$this->name = $name;
		$this->description = $description;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getDatetime() {
		return strtotime($this->datetime);
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getDescription() {
		return $this->description;
	}
	
	public function isHappening() {
		$now = date('U');
			
		if ($this->getDatetime() - 5 * 60 >= $now ||
			$this->getDatetime() + 1 * 60 * 60 >= $now) {
			return true;
		}
		
		return false;
	}
}
?>