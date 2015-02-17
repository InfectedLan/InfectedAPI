<?php
require_once 'objects/object.php';

class Location extends Object {
	private $name;
	private $title;
	
	/*
	 * Returns the name of this location.
	 */
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the title of this location.
	 */
	public function getTitle() {
		return $this->title;
	}
}
?>
