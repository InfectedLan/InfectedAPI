<?php
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'page.php';

class RestrictedPage extends Page {
	private $groupId;
	private $teamId;
	
	/*
	 * Returns the group of this page.
	 */
	public function getGroup() {
		return GroupHandler::getGroup($this->groupId);
	}
	
	/*
	 * Returns the team of this page.
	 */
	public function getTeam() {
		return TeamHandler::getTeam($this->teamId);
	}
}
?>