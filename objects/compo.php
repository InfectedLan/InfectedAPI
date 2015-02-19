<?php
require_once 'objects/eventobject.php';

class Compo extends EventObject {
	private $name;
	private $tag;
	private $descscription;
	private $startTime;
	private $registrationDeadline;
	private $teamSize;

	/*
	 * Returns the name of this compo.
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Returns the tag of this compo.
	 */
	public function getTag() {
		return $this->tag;
	}

	/*
	 * Returns the description of this compo.
	 */
	public function getDescription() {
		return $this->description;
	}

	/*
	 * Returns the startTime of this compo.
	 */
	public function getStartTime() {
		return $this->startTime;
	}

	/*
	 * Returns the registration deadline of this compo.
	 */
	public function getRegistrationDeadline() {
		return strtotime($this->registrationDeadline);
	}

	/*
	 * Returns the size of this team.
	 */
	public function getTeamSize() {
		return $this->teamSize;
	}

	/*
	 * Return a list of all matches for this compo.
	 */
	public function getMatches() {
		return MatchHandler::getMatchesByCompo($this);
	}
}
?>