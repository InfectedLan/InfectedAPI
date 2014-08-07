<?php
class Seatmap {
	private $id;
	private $human_name;
	private $background_image;
	
	public function __construct($id, $human_name, $background_image) {
		$this->id = $id;
		$this->human_name = $human_name;
		$this->background_image = $background_image;
	}

	public function getId() {
		return $this->id;
	}

	public function getHumanName() {
		return $this->human_name;
	}

	public function getBackgroundImage() {
		return $this->background_image;
	}
}
?>