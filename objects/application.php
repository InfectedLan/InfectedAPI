<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/userhandler.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/grouphandler.php';

class Application {
	private $id;
	private $userId;
	private $groupId;
	private $content;
	private $state;
	private $datetime;
	private $reason;
	
	public function Application($id, $userId, $groupId, $content, $state, $datetime, $reason) {
		$this->id = $id;
		$this->userId = $userId;
		$this->groupId = $groupId;
		$this->content = $content;
		$this->state = $state;
		$this->datetime = $datetime;
		$this->reason = $reason;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}
	
	public function getGroup() {
		return GroupHandler::getGroup($this->groupId);
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function getState() {
		return $this->state;
	}
	
	public function getDatetime() {
		return strtotime($this->datetime);
	}
	
	public function getReason() {
		return $this->reason;
	}
}
?>