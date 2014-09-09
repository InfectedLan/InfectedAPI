<?php
class Vote {
	private $id;
	private $userId;
	private $voteOptionId;

	public function __construct($id, $userId, $voteOptionId) {
		$this->id = $id;
		$this->userId = $userId;
		$this->voteOptionId = $voteOptionId;
	}

	public function getId() {
		return $this->id;
	}

	public function getUserId() {
		return $this->userId;
	}

	public function getVoteOptionId() {
		return $this->voteOptionId;
	}
}
?>