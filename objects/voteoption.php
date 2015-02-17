<?php
require_once 'handlers/compohandler.php';
require_once 'objects/object.php';

class VoteOption extends Object{
	private $compoId;
	private $thumbnailUrl;
	private $name;

	public function getCompo() {
		return CompoHandler::getCompo($this->compoId);
	}

	public function getThumbnailUrl() {
		return $this->thumbnailUrl;
	}

	public function getName() {
		return $this->name;
	}
}
?>