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

	/*
	 * Returns the last chat message for this chat.
	 */
	public function getLastMessage() {
		return ChatHandler::getLastChatMessage($this);
	}

	/*
	 * Returns the last chat messages, amount specified by count.
	 */
	public function getLastMessages($count) {
		return ChatHandler::getLastChatMessages($this, $count);
	}

	/*
	 * Returns true if the specified user is a member of this chat.
	 */
	public function isMember(User $user) {
		return ChatHandler::isChatMember($this, $user);
	}

	/*
	 * Add the specified user to this chat.
	 */
	public function addMember(User $user) {
		ChatHandler::addChatMember($this, $user);
	}

	/*
	 * Remove the specified user from this chat.
	 */
	public function removeMember(User $user) {
		ChatHandler::removeChatMember($this, $user);
	}

	/*
	 * Sends a message to this chat.
	 */
	public function sendMessage(User $user, $message) {
		ChatHandler::sendChatMessage($this, $user, $message);
	}
}
?>