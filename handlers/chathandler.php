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

require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/eventhandler.php';
require_once 'objects/chat.php';
require_once 'objects/event.php';
require_once 'objects/user.php';
require_once 'objects/chatmessage.php';

class ChatHandler {
	/*
	 * Get a chat by the internal id.
	 */
	public static function getChat($id) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_chats . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('Chat');
	}

	/*
	 * Return all chats.
	 */
	public static function getChatsByEvent(Event $event) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_chats . '`
																WHERE `eventId` = \'' . $event->getId() . '\';');

		$database->close();

		$chatList = array();

		while ($object = $result->fetch_object('Chat')) {
			array_push($chatList, $object);
		}

		return $chatList;
	}

	/*
	 * Return all chats.
	 */
	public static function getChats() {
		return self::getChatsByEvent(EventHandler::getCurrentEvent());
	}

	/*
	 * Creates a new chat and returns the object.
	 */
	public static function createChat($name, $title) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_chats . '` (`eventId`, `name`, `title`)
						  				VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
															\'' . $database->real_escape_string($name) . '\',
															\'' . $database->real_escape_string($title) . '\');');

		$database->close();

		return self::getChat($database->insert_id);
	}

	/*
	 * Remove a chat.
	 */
	public static function removeChat(Chat $chat) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_chats . '`
						  				WHERE `id` = \'' . $chat->getId() . '\';');

		$database->close();

		// Remove all chat messages for this chat.
		self::removeChatMessages($chat);

		// Remove all members from this chat.
		self::removeChatMembers($chat);
	}

	/*
	 * Returns true if the given user is member of the given chat.
	 */
	public static function isChatMemberByEvent(Chat $chat, Event $event, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_memberofchat . '`
																WHERE `eventId` = \'' . $event->getId() . '\'
																AND `userId` = \'' . $user->getId() . '\'
																AND `chatId` = \'' . $chat->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns true if the given user is member of the given chat.
	 */
	public static function isChatMember(Chat $chat, User $user) {
		return self::isChatMemberByEvent($chat, EventHandler::getCurrentEvent(), $user);
	}

	/*
	 * Returns an array of all members in the specificed chat.
	 */
	public static function getChatMembersByEvent(Chat $chat, Event $event) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberofchat . '`
																							WHERE `eventId` = \'' . $event->getId() . '\'
																							AND `chatId` = \'' . $chat->getId() . '\');');

		$database->close();

		$chatMemberList = array();

		while ($object = $result->fetch_object('User')) {
			array_push($chatMemberList, $object);
		}

		return $chatMemberList;
	}

	/*
	 * Returns an array of all members in the specificed chat.
	 */
	public static function getChatMembers(Chat $chat) {
		return self::getChatMembersByEvent($chat, EventHandler::getCurrentEvent());
	}

	/*
	 * Add the given user to the specified chat.
	 */
	public static function addChatMember(Chat $chat, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberofchat . '` (`eventId`, `userId`, `chatId`)
										  VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
															\'' . $user->getId() . '\',
												  		\'' . $chat->getId() . '\');');

		$database->close();
	}

	/*
	 * Remove the given user from the specified chat.
	 */
	public static function removeChatMemberByEvent(Chat $chat, Event $event, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_memberofchat . '`
											WHERE `eventId` = \'' . $event->getId() . '\'
											AND `userId` = \'' . $user->getId() . '\'
											AND `chatId` = \'' . $chat->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove the given user from the specified chat.
	 */
	public static function removeChatMember(Chat $chat, User $user) {
		self::removeChatMemberByEvent($chat, EventHandler::getCurrentEvent(), $user);
	}

	/*
	 * Add the given user to the specified chat.
	 */
	public static function addChatMembers(Chat $chat, array $userList) {
		$database = Database::open(Settings::db_name_infected_compo);

		foreach ($userList as $user) {
			$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberofchat . '` (`eventId`, `userId`, `chatId`)
												VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
																\'' . $user->getId() . '\',
																\'' . $chat->getId() . '\');');
		}

		$database->close();
	}

	/*
	 * Remove members from the given chat.
	 */
	public static function removeChatMembersByEvent(Chat $chat, Event $event) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_memberofchat . '`
											WHERE `eventId` = \'' . $event->getId() . '\'
											AND `chatId` = \'' . $chat->getId() . '\';');

		$database->close();
	}

	/*
	 * Remove members from the given chat.
	 */
	public static function removeChatMembers(Chat $chat) {
		self::removeChatMembersByEvent($chat, EventHandler::getCurrentEvent());
	}

	/*
	 * Return the chat message with the given id.
	 */
	public static function getChatMessage($id) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_chatmessages . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		$database->close();

		return $result->fetch_object('ChatMessage');
	}

	/*
	 * Return the chat message with the given id.
	 */
	public static function getChatMessagesByEvent(Event $event) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_chatmessages . '`
																WHERE `eventId` = \'' . $event->getId() . '\';');

		$database->close();

		$chatMessageList = array();

		while ($object = $result->fetch_object('ChatMessage')) {
			array_push($chatMessageList, $object);
		}

		return $chatMessageList;
	}

	/*
	 * Return the chat message with the given id.
	 */
	public static function getChatMessages() {
		return self::getChatMessagesByEvent(EventHandler::getCurrentEvent());
	}

	/*
	 * Returns the last chat messages for the given chat.
	 */
	public static function getLastChatMessage(Chat $chat) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_chatmessages . '`
																WHERE `chatId` = \'' . $chat->getId() . '\'
																ORDER BY `id` DESC
																LIMIT 1;');

		$database->close();

		return $result->fetch_object('ChatMessage');
	}

	/*
	 * Returns an array of the last given number of chat messages for given chat.
	 */
	public static function getLastChatMessages(Chat $chat, $count) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_chatmessages . '`
																WHERE `chatId` = \'' . $chat->getId() . '\'
																ORDER BY `id` DESC
																LIMIT ' . $database->real_escape_string($count) . ';');

		$database->close();

		$chatMessageList = array();

		while ($object = $result->fetch_object('ChatMessage')) {
			array_push($chatMessageList, $object);
		}

		return $chatMessageList;
	}

	/*
	 * Remove chat messages for given chat.
	 */
	public static function removeChatMessages(Chat $chat) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_chatmessages . '`
						  				WHERE `chatId` = \'' . $chat->getId() . '\';');

		$database->close();
	}

	/*
	 * Send a massage to this chat from the given user.
	 */
	public static function sendChatMessage(Chat $chat, User $user, $message) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_chatmessages . '` (`eventId`, `userId`, `chatId`, `time`, `message`)
										  VALUES (\'' . EventHandler::getCurrentEvent()->getId() . '\',
															\'' . $user->getId() . '\',
														  \'' . $chat->getId() . '\',
														  \'' . date('Y-m-d H:i:s') . '\',
														  \'' . htmlspecialchars($database->real_escape_string($message), ENT_QUOTES | ENT_HTML401) . '\');');

		$database->close();
	}
}
?>
