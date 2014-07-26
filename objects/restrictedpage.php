<?php
class RestrictedPage extends Page {
	private $groupId;
	private $teamId;
	
	public function __construct($id, $name, $title, $content, $groupId, $teamId) {
		parent::__construct($id, $name, $title, $content);
	
		$this->groupId = $groupId;
		$this->teamId = $teamId;
	}
	
	public function getGroup() {
		return $this->groupId;
	}
	
	public function getTeam() {
		return $this->teamId;
	}
}
?>