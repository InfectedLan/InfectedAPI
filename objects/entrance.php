<?php
require_once 'objects/object.php';

class Entrance extends Object {
	private $name;
	private $title;
	
	/*
	 * Returns the name of this entrance.
	 */
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the title of this entrance.
	 */
	public function getTitle() {
		return $this->title;
	}
}
?>