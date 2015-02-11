<?php
require_once 'objects/object.php';

class Compo extends Object {
	private $startTime;
	private $registrationDeadline;
	private $name;
	private $desc;
	private $event;
	private $teamSize;
	private $tag;

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