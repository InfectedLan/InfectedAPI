<?php
require_once 'handlers/chathandler.php';
require_once 'objects/object.php';

class Chat extends Object {
	private $name;
	private $title;
	
	public function __construct($id, $name, $title) {
		parent::__construct($id);
		$this->name = $name;
		$this->title = $title;
	}
	
	/*
	 * Returns the name of this chat.
	 */
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the name of this chat.
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