<?php
require_once 'objects/object.php';

class ReadyHandler extends Object{
	private $compoId;

	public function getCompoId() {
		return $this->compoId;
	}
}
?>