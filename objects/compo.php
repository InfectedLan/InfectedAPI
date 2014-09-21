<?php
class Compo {
	private $id;
	private $startTime;
	private $registrationDeadline;
	private $name;
	private $desc;
	private $event;
	private $teamSize;
	private $tag;

	public function __construct($id, $startTime, $registrationDeadline, $name, $desc, $event, $teamSize, $tag) {
		$this->id = $id;
		$this->startTime = $startTime;
		$this->registrationDeadline = $registrationDeadline;
		$this->name = $name;
		$this->desc = $desc;
		$this->event = $event;
		$this->teamSize = $teamSize;
		$this->tag = $tag;
	}

	public function getId() {
		return $this->id;
	}

	public function getStartTime() {
		return $this->startTime;
	}

	public function getRegistrationDeadline() {
		return $this->registrationDeadline;
	}

	public function getName() {
		return $this->name;
	}

	public function getDesc() {
		return $this->desc;
	}

	public function getEvent() {
		return $this->event;
	}

	public function getTeamSize() {
		return $this->teamSize;
	}

	public function getTag() {
		return $this->tag;
	}
}
?>