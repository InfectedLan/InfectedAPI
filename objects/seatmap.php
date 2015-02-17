<?php
require_once 'objects/object.php';

class Seatmap extends Object {
	private $humanName;
	private $backgroundImage;

	/*
	 * Returns the name of this seatmap.
	 */
	public function getHumanName() {
		return $this->humanName;
	}

	/*
	 * Returns the background image for this seatmap.
	 */
	public function getBackgroundImage() {
		return $this->backgroundImage;
	}
}
?>