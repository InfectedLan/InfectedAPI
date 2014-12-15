<?php
require_once 'objects/object.php';

class Seatmap extends Object {
	private $human_name;
	private $background_image;
	
	public function __construct($id, $human_name, $background_image) {
		parent::__construct($id);
		
		$this->human_name = $human_name;
		$this->background_image = $background_image;
	}

	public function getHumanName() {
		return $this->human_name;
	}

	public function getBackgroundImage() {
		return $this->background_image;
	}
}
?>