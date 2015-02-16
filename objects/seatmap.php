<?php
require_once 'objects/object.php';

class Seatmap extends Object {
	private $human_name;
	private $background_image;

	public function getHumanName() {
		return $this->human_name;
	}

	public function getBackgroundImage() {
		return $this->background_image;
	}
}
?>