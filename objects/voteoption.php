<?php
require_once 'handlers/compohandler.php';
require_once 'objects/object.php';

class VoteOption extends Object{
	private $compoId;
	private $thumbnailUrl;
	private $name;

	/*
	 * Returns the compo of this vote option.
	 */
	public function getCompo() {
		return CompoHandler::getCompo($this->compoId);
	}

	/*
	 * Return the thumbnail url for this vote option.
	 */
	public function getThumbnailUrl() {
		return $this->thumbnailUrl;
	}

	/*
	 * Returns the name of this vote option.
	 */
	public function getName() {
		return $this->name;
	}
}
?>