<?php
require_once 'page.php';

class RestrictedPage extends Page {
	private $groupId;
	private $teamId;
	private $isPrivate;
	
	public function __construct($id, $name, $title, $content, $groupId, $teamId, $isPrivate) {
		parent::__construct($id, $name, $title, $content);
	
		$this->groupId = $groupId;
		$this->teamId = $teamId;
		$this->isPrivate = $isPrivate;
	}
	
	public function getGroup() {
		return $this->groupId;
	}
	
	public function getTeam() {
		return $this->teamId;
	}
	
	public function getPrivate() {
		return $this->isPrivate;
	}
}
?>