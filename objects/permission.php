<?php
require_once 'objects/object.php';

class Permission extends Object {
	private $value;
	private $description;
	
	/*
	 * Returns the value of this permission.
	 */
	public function getValue() {
		return $this->value;
	}
	
	/*
	 * Returns the description of this permission.
	 */
	public function getDescription() {
		return $this->description;
	}
}
?>