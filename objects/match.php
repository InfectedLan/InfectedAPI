<?php
class Match {
	private $id;
	private $scheduledTime;
	private $connectDetails;
	private $winner;

	public function __construct($id, $scheduledTime, $connectDetails, $winner) {
		$this->id = $id;
		$this->scheduledTime = $scheduledTime;
		$this->connectDetails = $connectDetails;
		$this->winner = $winner;
	}

	public function getId() {
		return $this->id;
	}

	public function getScheduledTime() {
		return $this->scheduledTime;
	}

	public function getConnectDetails() {
		return $this->connectDetails;
	}

	public function getWinner() {
		return $this->winner;
	}
}
?>