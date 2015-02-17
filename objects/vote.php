<?php
require_once 'handlers/userhandler.php';
require_once 'objects/object.php';

class Vote extends Object {
	private $consumerId;
	private $voteOptionId;

	/*
	 * Returns the consumer of this vote.
	 */
	public function getConsumer() {
		return UserHandler::getUser($this->consumerId);
	}

	/*
	 * Returns the voteoption of this vote.
	 */
	public function getVoteOption() {
		return VoteOptionHandler::getVoteOption($this->voteOptionId);
	}
}
?>