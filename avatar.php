<?php
require_once 'database.php';

class Avatar {
	private $database;
	
	private $id;
	private $userId;
	private $relativeUrl;
	private $state;

	public function Avatar($id, $userId, $relativeUrl, $state) {
		$this->database = new Database();
		
		$this->id = $id;
		$this->userId = $userId;
		$this->relativeUrl = $relativeUrl;
		$this->state = $state;
	}

	public function getId() {
		return $this->id;
	}
	
	public function getUser() {
		return $this->database->getUser($this->userId);
	}

	public function getRelativeUrl() {
		return $this->relativeUrl;
	}

	public function getState() {
		return $this->state;
	}
	
	// TODO: this.
	public function setState($newstatus) {
		if (is_bool($newstatus)) {
			mysql_query("UPDATE `avatars` SET `state` = '" . $newstatus . "' WHERE `id` = '" . $this->id . "';");
			$state = $newstatus;
		}
	}
}
?>