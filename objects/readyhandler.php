<?php
require_once 'objects/object.php';

class ReadyHandler extends Object{
	private $compoId;

	public function __construct($id, $compoId) {
		parent::__construct($id);
		
		$this->compoId = $compoId;
	}

	public function getCompoId() {
		return $this->compoId;
	}
}
?>