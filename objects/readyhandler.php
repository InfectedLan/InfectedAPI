<?php
require_once 'handlers/compohandler.php';
require_once 'objects/object.php';

class ReadyHandler extends Object {
	private $compoId;

	/*
	 * Returns the compo that this readyhandler is for.
	 */
	public function getCompo() {
		return CompoHandler::getCompo($this->compoId);
	}
}
?>