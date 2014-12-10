<?php
require_once 'handlers/eventhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';

class Application {
	private $id;
	private $eventId;
	private $userId;
	private $groupId;
	private $content;
	private $datetime;
	private $state;
	private $reason;
	private $queued;
	
	public function __construct($id, $eventId, $userId, $groupId, $content, $datetime, $state, $reason, $queued) {
		$this->id = $id;
		$this->eventId = $eventId;
		$this->userId = $userId;
		$this->groupId = $groupId;
		$this->content = $content;
		$this->datetime = $datetime;
		$this->state = $state;
		$this->reason = $reason;
		$this->queued = $queued;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getEvent() {
		return EventHandler::getEvent($this->eventId);
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
	
	public function getDatetime() {
		return strtotime($this->datetime);
	}
	
	public function getState() {
		return $this->state;
	}
	
	public function getReason() {
		return $this->reason;
	}
	
	public function isQueued() {
		return $this->queued ? true : false;
	}
}
?>