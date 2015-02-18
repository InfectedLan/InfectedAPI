<?php
require_once 'handlers/seatmaphandler.php';
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

	/*
	 * Sets the background image for this seatmap.
	 */
	public function setBackgroundImage($filename) {
		SeatmapHandler::setBackground($this, $filename);
	}

	/*
	 * Returns the event this seatmap is accosiated with.
	 */
	public function getEvent() {
		return SeatmapHandler::getEvent($this);
	}

	/*
	 * Add an row to this seatmap at the specified coordinates.
	 */
	public function addRow($x, $y) {
		return RowHandler::createRow($this, $x, $y);
	}
}
?>