<?php
require_once 'page.php';

class CrewPage extends Page {
	private $groupId;
	private $teamId;
	
	public function CrewPage($id, $name, $title, $content, $groupId, $teamId) {
		parent::__construct($id, $name, $title, $content);
		
		$this->groupId = $groupId;
		$this->teamId = $teamId;
	}
	
	public function display() {
		// Format the page with HTML.
		echo '<h1>' . $this->getTitle() . '</h1>';
		echo $this->getContent();
	}

	public function getGroup() {
		return $this->groupId;
	}
	
	public function getTeam() {
		return $this->teamId;
	}
}
?>