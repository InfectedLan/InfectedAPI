<?php
require_once 'objects/eventobject.php';

class Compo extends EventObject {
	private $name;
	private $tag;
	private $descscription;
	private $startTime;
	private $registrationDeadline;
	private $teamSize;

	public function getName() {
		return $this->name;
	}

	public function getTag() {
		return $this->tag;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getStartTime() {
		return $this->startTime;
	}

	public function getRegistrationDeadline() {
		return $this->registrationDeadline;
	}

	public function getTeamSize() {
		return $this->teamSize;
	}

	/*
	 * Return a list of all matches for this compo.
	 */ 
	public function getMatches() {
		return MatchHandler::getMatchesForCompo($this);
	}
}
?>