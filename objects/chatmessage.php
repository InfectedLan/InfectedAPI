<?php
require_once 'handlers/userhandler.php';
require_once 'handlers/chathandler.php';
require_once 'objects/object.php';

class ChatMessage extends Object {
	private $userId;
	private $chatId;
	private $message;
	
	public function __construct($id, $userId, $chatId, $message) {
		parent::__construct($id);
		$this->userId = $userId;
		$this->chatId = $chatId;
		$this->message = $message;
	}
	
	/*
	 * Returns the user who sent this chat message.
	 */
	public function getUser() {
		return UserHandler::getUser($this->userId);
	}
	
	/*
	 * Returns the chat that this chat message belongs to.
	 */
	public function getChat() {
		return ChatHandler::getChat($this->chatId);
	}

	/*
	 * Returns the message.
	 */
	public function getMessage() {
		return $this->message;
	}
}
?>