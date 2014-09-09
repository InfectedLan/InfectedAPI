<?php
//Used by the new compo system
class VoteOption {
	private $id;
	private $compoId;
	private $thumbnailUrl;
	private $name;

	public function __construct($id, $compoId, $thumbnailUrl, $name) {
		$this->id = $id;
		$this->compoId = $compoId;
		$this->thumbnailUrl = $thumbnailUrl;
		$this->name = $name;
	}

	public function getId() {
		return $this->id;
	}

	public function getCompoId() {
		return $this->compoId;
	}

	public function getThumbnailUrl() {
		return $this->thumbnailUrl;
	}

	public function getName() {
		return $this->name;
	}
}
?>