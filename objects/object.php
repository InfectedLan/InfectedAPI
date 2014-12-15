<?php
class Object {
	private $id;
	
	public function __construct($id) {
		$this->id = $id;
	}
	
	/*
	 * Retuns the internal id for this object.
	 */
	public function getId() {
		return $this->id;
	}
}
?>