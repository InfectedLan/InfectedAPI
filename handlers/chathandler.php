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
require_once 'objects/chat.php';
require_once 'objects/user.php';
require_once 'objects/chatmessage.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/eventhandler.php';

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
	public static function getChats() {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_chats . '`;');

		$database->close();

		$chatList = [];

		while ($object = $result->fetch_object('Chat')) {
			$chatList[] = $object;
		}

		return $chatList;
	}

	/*
	 * Creates a new chat and returns the object.
	 */
	public static function createChat($name, $title) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_chats . '` (`name`, `title`)
						  				VALUES (\'' . $database->real_escape_string($name) . '\',
															\'' . $database->real_escape_string($title) . '\');');
        
        $chat = self::getChat( $database->insert_id );

        $database->close();       

		return $chat;
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
	public static function isChatMember(Chat $chat, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_memberofchat . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `chatId` = \'' . $chat->getId() . '\';');

		$database->close();

		return $result->num_rows > 0;
	}

	/*
	 * Returns an array of all members in the specificed chat.
	 */
	public static function getChatMembers(Chat $chat) {
		$database = Database::open(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_users . '`
																WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . Settings::db_table_infected_compo_memberofchat . '`
																							WHERE `chatId` = \'' . $chat->getId() . '\');');

		$database->close();

		$chatMemberList = [];

		while ($object = $result->fetch_object('User')) {
			$chatMemberList[] = $object;
		}

		return $chatMemberList;
	}

	/*
	 * Add the given user to the specified chat.
	 */
	public static function addChatMember(Chat $chat, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberofchat . '` (`userId`, `chatId`)
										  VALUES (\'' . $user->getId() . '\',
												  		\'' . $chat->getId() . '\');');

		$database->close();
	}

	/*
	 * Remove the given user from the specified chat.
	 */
	public static function removeChatMember(Chat $chat, User $user) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_memberofchat . '`
											WHERE `userId` = \'' . $user->getId() . '\'
											AND `chatId` = \'' . $chat->getId() . '\';');

		$database->close();
	}

	/*
	 * Add the given user to the specified chat.
	 */
	public static function addChatMembers(Chat $chat, array $userList) {
		$database = Database::open(Settings::db_name_infected_compo);

		foreach ($userList as $user) {
			$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberofchat . '` (`userId`, `chatId`)
												VALUES (\'' . $user->getId() . '\',
																\'' . $chat->getId() . '\');');
		}

		$database->close();
	}

	/*
	 * Remove members from the given chat.
	 */
	public static function removeChatMembers(Chat $chat) {
		$database = Database::open(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_memberofchat . '`
											WHERE `chatId` = \'' . $chat->getId() . '\';');

		$database->close();
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
	public static function getChatMessages() {
		$database = Database::open(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_chatmessages . '`;');

		$database->close();

		$chatMessageList = [];

		while ($object = $result->fetch_object('ChatMessage')) {
			$chatMessageList[] = $object;
		}

		return $chatMessageList;
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

		$chatMessageList = [];

		while ($object = $result->fetch_object('ChatMessage')) {
			$chatMessageList[] = $object;
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

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_chatmessages . '` (`userId`, `chatId`, `time`, `message`)
										  VALUES (\'' . $user->getId() . '\',
														  \'' . $chat->getId() . '\',
														  \'' . date('Y-m-d H:i:s') . '\',
														  \'' . htmlspecialchars($database->real_escape_string($message), ENT_QUOTES | ENT_HTML401) . '\');');

		$database->close();
	}

    public static function canChat(Chat $chat, User $user) {
        if ($user->hasPermission('*') ||
            $user->hasPermission('compo.chat') ||
            $chat->isMember($user)) {
            return true;
        }

				return false;
    }

    public static function canRead(Chat $chat, User $user) {
        if (self::canChat($chat, $user)) {
          return true;
        } else {
            //You can also read the chat if it is a compo chat for a compo you are currently participating in. Soooo....
            $clanList = ClanHandler::getClansByUser($user);

						foreach ($clanList as $clan) {
                            if ($clan->isQualified($clan->getCompo()) && $clan->getCompo()->getChat()->getId() == $chat->getId()) {
                    return true;
                }
            }
        }
    }
}
?>
