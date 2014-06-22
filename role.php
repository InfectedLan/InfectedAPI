<?php
class Role {
	private $id;
	private $name;
	
	public function Role($id, $name) {
		$this->id = $id;
		$this->name = $name;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
}
?>