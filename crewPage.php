<?php
class CrewPage {
	private $id;
	private $name;
	private $title;
	private $content;
	private $groupId;
	private $teamId;
	
	public function CrewPage($id, $name, $title, $content, $groupId, $teamId) {
		$this->id = $id;
		$this->name = $name;
		$this->title = $title;
		$this->content = $content;
		$this->groupId = $groupId;
		$this->teamId = $teamId;
	}
	
	public function display() {
		// Format the page with HTML.
		echo '<h1>' . $this->getTitle() . '</h1>';
		echo $this->getContent();
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getTitle() {
		return $this->title;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function getGroup() {
		return $this->groupId;
	}
	
	public function getTeam() {
		return $this->teamId;
	}
}
?>