<?php
require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'page.php';

class RestrictedPage extends Page {
	private $groupId;
	private $teamId;
	
	public function __construct($id, $name, $title, $content, $groupId, $teamId) {
		parent::__construct($id, $name, $title, $content);
	
		$this->groupId = $groupId;
		$this->teamId = $teamId;
	}
	
	public function getGroup() {
		return GroupHandler::getGroup($this->groupId);
	}
	
	public function getTeam() {
		return TeamHandler::getTeam($this->teamId);
	}
}
?>