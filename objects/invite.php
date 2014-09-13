<?php
class Invite {
	private $id;
	private $userId;
	private $clanId;

	public function __construct($id, $userId, $clanId) {
		$this->id = $id;
		$this->userId = $userId;
		$this->clanId = $clanId;
	}

	public function getId() {
		return $this->id;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function getClanId() {
		return $this->clanId;
	}
}
?>