<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'handlers/userhandler.php';
require_once 'handlers/chathandler.php';
require_once 'objects/object.php';

class ChatMessage extends Object {
	private $userId;
	private $chatId;
	private $time;
	private $message;
	
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
	 * Returns the time this message was sent.
	 */
	public function getTime() {
		return strtotime($this->time);
	}

	/*
	 * Returns the message.
	 */
	public function getMessage() {
		return $this->message;
	}
}
?>