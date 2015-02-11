<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'objects/object.php';

class Match extends Object {
	const STATE_READYCHECK = 0;
	const STATE_CUSTOM_PREGAME = 1;
	const STATE_JOIN_GAME = 2;

	private $scheduledTime;
	private $connectDetails;
	private $winner;
	private $state;
	private $compoId;
	private $bracketOffset;
	private $chat;

	public function getChat() {
		return $this->chat;
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

	public function getState() {
		return $this->state;
	}

	public function getBracketOffset() {
		return $this->bracketOffset;
	}

	public function isParticipant($user) {
		//Get list of clans
		$participants = MatchHandler::getParticipants($this);

		foreach($participants as $clan) {
			if(ClanHandler::isMember($user, $clan)) {
				return true;
			}
		}
	}

	//Returns true if the match can be run
	public function isReady() {
		return MatchHandler::isReady($this);
	}

	public function setState($newState) {
		$con = MySQL::open(Settings::db_name_infected_compo);

		$result = mysqli_query($con, 'UPDATE `' . Settings::db_table_infected_compo_matches . '` SET `state` = ' . $con->real_escape_string($newState) . ' WHERE `id` = ' . $this->id . ';');

		MySQL::close($con);
	}

	public function getCompoId() {
		return $this->compoId;
	}
}
?>