<?php
require_once 'objects/object.php';

class Vote extends Object {
	private $consumerId;
	private $voteOptionId;

	public function getConsumerId() {
		return $this->consumerId;
	}

	public function getVoteOptionId() {
		return $this->voteOptionId;
	}
}
?>