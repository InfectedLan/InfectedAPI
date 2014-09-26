<?php
class Vote {
	private $id;
	private $consumerId;
	private $voteOptionId;

	public function __construct($id, $consumerId, $voteOptionId) {
		$this->id = $id;
		$this->consumerId = $consumerId;
		$this->voteOptionId = $voteOptionId;
	}

	public function getId() {
		return $this->id;
	}

	public function getConsumerId() {
		return $this->consumerId;
	}

	public function getVoteOptionId() {
		return $this->voteOptionId;
	}
}
?>