<?php
require_once 'objects/object.php';

class Vote extends Object {
	private $consumerId;
	private $voteOptionId;

	public function __construct($id, $consumerId, $voteOptionId) {
		parent::__construct($id);
	
		$this->consumerId = $consumerId;
		$this->voteOptionId = $voteOptionId;
	}

	public function getConsumerId() {
		return $this->consumerId;
	}

	public function getVoteOptionId() {
		return $this->voteOptionId;
	}
}
?>