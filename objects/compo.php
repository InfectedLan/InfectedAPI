<?php
class Compo {
	private $id;
	private $startTime;
	private $registrationDeadline;
	private $name;
	private $desc;
	private $event;

	public function __construct($id, $startTime, $registrationDeadline, $name, $desc, $event) {
		$this->id = $id;
		$this->startTime = $startTime;
		$this->registrationDeadline = $registrationDeadline;
		$this->name = $name;
		$this->desc = $desc;
		$this->event = $event;
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
}
?>