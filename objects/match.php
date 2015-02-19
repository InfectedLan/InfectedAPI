<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/chathandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/clanhandler.php';
require_once 'objects/object.php';

class Match extends Object {
	const STATE_READYCHECK = 0;
	const STATE_CUSTOM_PREGAME = 1;
	const STATE_JOIN_GAME = 2;

	const BRACKET_WINNER = 1;
	const BRACKET_LOOSER = 0;

	private $scheduledTime;
	private $connectDetails;
	private $winner;
	private $state;
	private $compoId;
	private $bracketOffset;
	private $chatId;
	private $bracket;

	public function getBracket() {
		return $this->bracket;
	}

	public function getChat() {
		return ChatHandler::getChat($this->chatId);
	}
	public function getScheduledTime() {
		return strtotime($this->scheduledTime);
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
		foreach (MatchHandler::getParticipants($this) as $clan) {
			return $clan->isMember($user);
		}
	}

	//Returns true if the match can be run
	public function isReady() {
		return MatchHandler::isReady($this);
	}

	public function setState($newState) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('UPDATE `' . Settings::db_table_infected_compo_matches . '`
							        SET `state` = \'' . $database->real_escape_string($newState) . '\'
							        WHERE `id` = \'' . $this->getId() . '\';');

		$database->close();
	}

	public function getCompo() {
		return CompoHandler::getCompo($this->compoId);
	}

	public function getParents() {
		return MatchHandler::getMatchParents($this);
	}
}
?>