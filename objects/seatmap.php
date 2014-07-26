<?php
class Seatmap {
	private $id;
	private $human_name;
	
	public function __construct($id, $human_name) {
		$this->id = $id;
		$this->human_name = $human_name;
	}

	public function getId() {
		return $this->id;
	}

	public function getHumanName() {
		return $this->human_name;
	}
}
?>