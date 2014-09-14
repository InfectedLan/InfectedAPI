<?php
class ReadyHandler {
	private $id;
	private $compoId;

	public function __construct($id, $compoId) {
		$this->id = $id;
		$this->compoId = $compoId;
	}

	public function getId() {
		return $this->id;
	}

	public function getCompoId() {
		return $this->compoId;
	}
}
?>