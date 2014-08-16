<?php
class Permission {
	private $id;
	private $value;
	private $description;
	
	public function __construct($id, $value, $description) {
		$this->id = $id;
		$this->value = $value;
		$this->description = $description;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getDescription() {
		return $this->description;
	}
}
?>