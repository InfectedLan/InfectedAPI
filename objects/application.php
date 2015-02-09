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
	private $updatedByUserId;
	private $comment;
	
	public function __construct($id, $eventId, $groupId, $userId, $openedTime, $closedTime, $state, $content, $updatedByUserId, $comment) {
		parent::__construct($id);
		
		$this->eventId = $eventId;
		$this->groupId = $groupId;
		$this->userId = $userId;
		$this->openedTime = $openedTime;
		$this->closedTime = $closedTime;
		$this->state = $state;
		$this->content = $content;
		$this->updatedByUserId = $updatedByUserId;
		$this->comment = $comment;
	}
	
	/*
	 * Returns the event this application was submitted to.
	 */
	public function getEvent() {
		return EventHandler::getEvent($this->eventId);
	}
	
	/*
	 * Returns the group that this application is for.
	 */
	public function getGroup() {
		return GroupHandler::getGroup($this->groupId);
	}
	
	/*
	 * Returns the user which opened this application.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}
	
	/*
	 * Returns the time when this application was opened.
	 */
	public function getOpenedTime() {
		return strtotime($this->openedTime);
	}
	
	/*
	 * Returns the time when this application was closed.
	 */
	public function getClosedTime() {
		return strtotime($this->closedTime);
	}
	
	/*
	 * Returns the state of this application.
	 */
	public function getState() {
		return $this->state;
	}
	
	/*
	 * Returns the state of this application.
	 */
	public function getStateAsString() {
		$updatedByUser = $this->getUpdatedByUser();
		
		if ($this->isQueued()) {
			return 'Står i kø';
		} else {
			switch ($this->getState()) {
				case 1:
					return 'Ubehandlet';
					break;
					
				case 2:
					return 'Godkjent' . ($updatedByUser != null ? ' av ' . $updatedByUser->getDisplayName() : null);
					break;
					
				case 3:
					return 'Avslått' . ($updatedByUser != null ? ' av ' . $updatedByUser->getDisplayName() : null);
					break;
			}
		}
	}
	
	/*
	 * Returns the content of this application.
	 */
	public function getContent() {
		return $this->content;
	}
	
	/*
	 * Returns the user that last updated this application.
	 */
	public function getUpdatedByUser() {
		return UserHandler::getUser($this->updatedByUserId);
	}
	
	/*
	 * Returns the comment of this application.
	 */
	public function getComment() {
		return $this->comment;
	}
	
	/*
	 * Returns true if this application is in a queue, otherwise false.
	 */
	public function isQueued() {
		return ApplicationHandler::isQueued($this);
	}
}
?>