<?php
require_once 'handlers/eventhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/userhandler.php';
require_once 'objects/object.php';

class Application extends Object {
	private $eventId;
	private $groupId;
	private $userId;
	private $openedTime;
	private $closedTime;
	private $state;
	private $content;
	private $comment;
	
	public function __construct($id, $eventId, $groupId, $userId, $openedTime, $closedTime, $state, $content, $comment) {
		parent::__construct($id);
		
		$this->eventId = $eventId;
		$this->groupId = $groupId;
		$this->userId = $userId;
		$this->openedTime = $openedTime;
		$this->closedTime = $closedTime;
		$this->state = $state;
		$this->content = $content;
		$this->comment = $comment;
	}
	
	public function getEvent() {
		return EventHandler::getEvent($this->eventId);
	}
	
	public function getGroup() {
		return GroupHandler::getGroup($this->groupId);
	}
	
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}
	
	public function getOpenedTime() {
		return strtotime($this->openedTime);
	}
	
	public function getClosedTime() {
		return strtotime($this->closedTime);
	}
	
	public function getState() {
		return $this->state;
	}
	
	public function isQueued() {
		return ApplicationHandler::isQueued($this);
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function getComment() {
		return $this->comment;
	}
}
?>