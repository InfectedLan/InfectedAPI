<?php
require_once 'handlers/chathandler.php';
require_once 'objects/object.php';

class Chat extends Object {
	private $name;
	private $title;
	
	/*
	 * Returns the name of this object.
	 */
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the name of this object.
	 */
	public function getTitle() {
		return $this->title;
	}
	/*
	 * Returns a list of all members in this chat.
	 */
	public function getMembers() {
		return ChatHandler::getChatMembers($this);
	}
}
?>