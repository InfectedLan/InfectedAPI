<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'databaseconstants.php';
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
	public static function getChat(int $id): ?Chat {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_chats . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('Chat');
	}

	/*
	 * Return all chats.
	 */
	public static function getChats(): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_chats . '`;');

		$chatList = [];

		while ($object = $result->fetch_object('Chat')) {
			$chatList[] = $object;
		}

		return $chatList;
	}

	/*
	 * Creates a new chat and returns the object.
	 */
	public static function createChat(string $name, string $title): Chat {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_chats . '` (`name`, `title`)
						  				VALUES (\'' . $database->real_escape_string($name) . '\',
															\'' . $database->real_escape_string($title) . '\');');

		return self::getChat($database->insert_id);
	}

	/*
	 * Remove a chat.
	 */
	public static function removeChat(Chat $chat) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_compo_chats . '`
						  				WHERE `id` = \'' . $chat->getId() . '\';');

		// Remove all chat messages for this chat.
		self::removeChatMessages($chat);

		// Remove all members from this chat.
		self::removeChatMembers($chat);
	}

	/*
	 * Returns true if the given user is member of the given chat.
	 */
	public static function isChatMember(Chat $chat, User $user): bool {
	    return true; //Remove later
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT `id` FROM `' . DatabaseConstants::db_table_infected_compo_memberofchat . '`
																WHERE `userId` = \'' . $user->getId() . '\'
																AND `chatId` = \'' . $chat->getId() . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns an array of all members in the specificed chat.
	 */
	public static function getChatMembers(Chat $chat): array {
		$database = Database::getConnection(Settings::db_name_infected);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_users . '`
																WHERE `id` = (SELECT `userId` FROM `' . Settings::db_name_infected_compo . '`.`' . DatabaseConstants::db_table_infected_compo_memberofchat . '`
																							WHERE `chatId` = \'' . $chat->getId() . '\');');

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
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_memberofchat . '` (`userId`, `chatId`)
										  VALUES (\'' . $user->getId() . '\',
												  		\'' . $chat->getId() . '\');');
	}

	/*
	 * Same as above, but with id's to save 2 queries
	 */
	public static function addChatMemberById(int $chatId, int $userId) {
	    $database = Database::getConnection(Settings::db_name_infected_compo);

	    $database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_memberofchat . '` (`userId`, `chatId`)
										  VALUES (\'' . $database->real_escape_string($userId) . '\',
												  		\'' . $database->real_escape_string($chatId) . '\');');
	}

	/*
	 * Remove the given user from the specified chat.
	 */
	public static function removeChatMember(Chat $chat, User $user) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_compo_memberofchat . '`
											WHERE `userId` = \'' . $user->getId() . '\'
											AND `chatId` = \'' . $chat->getId() . '\';');
	}

	/*
	 * Add the given user to the specified chat.
	 */
	public static function addChatMembers(Chat $chat, array $userList) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		foreach ($userList as $user) {
			$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_memberofchat . '` (`userId`, `chatId`)
												VALUES (\'' . $user->getId() . '\',
																\'' . $chat->getId() . '\');');
		}
	}

	/*
	 * Remove members from the given chat.
	 */
	public static function removeChatMembers(Chat $chat) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_compo_memberofchat . '`
											WHERE `chatId` = \'' . $chat->getId() . '\';');
	}

	/*
	 * Return the chat message with the given id.
	 */
	public static function getChatMessage(int $id): ?ChatMessage {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_chatmessages . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

		return $result->fetch_object('ChatMessage');
	}

	/*
	 * Return the chat message with the given id.
	 */
	public static function getChatMessages(): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_chatmessages . '`;');

		$chatMessageList = [];

		while ($object = $result->fetch_object('ChatMessage')) {
			$chatMessageList[] = $object;
		}

		return $chatMessageList;
	}

	/*
	 * Returns the last chat messages for the given chat.
	 */
	public static function getLastChatMessage(Chat $chat): ?ChatMessage {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_chatmessages . '`
																WHERE `chatId` = \'' . $chat->getId() . '\'
																ORDER BY `id` DESC
																LIMIT 1;');

		return $result->fetch_object('ChatMessage');
	}

	/*
	 * Returns an array of the last given number of chat messages for given chat.
	 */
	public static function getLastChatMessages(Chat $chat, int $count): array {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . DatabaseConstants::db_table_infected_compo_chatmessages . '`
																WHERE `chatId` = \'' . $chat->getId() . '\'
																ORDER BY `id` DESC
																LIMIT ' . $database->real_escape_string($count) . ';');

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
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . DatabaseConstants::db_table_infected_compo_chatmessages . '`
						  				WHERE `chatId` = \'' . $chat->getId() . '\';');

	}

	/*
	 * Send a massage to this chat from the given user.
	 */
	public static function sendChatMessage(Chat $chat, User $user, string $message) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . DatabaseConstants::db_table_infected_compo_chatmessages . '` (`userId`, `chatId`, `time`, `message`)
										  VALUES (\'' . $user->getId() . '\',
														  \'' . $chat->getId() . '\',
														  \'' . date('Y-m-d H:i:s') . '\',
														  \'' . htmlspecialchars($database->real_escape_string($message), ENT_QUOTES | ENT_HTML401) . '\');');

	}

  public static function canChat(Chat $chat, User $user): bool {
    return $user->hasPermission('compo.chat') || $chat->isMember($user);
  }

  public static function canRead(Chat $chat, User $user): bool {
    if (self::canChat($chat, $user)) {
      return true;
    } else {
      // You can also read the chat if it is a compo chat for a compo you are currently participating in. Soooo....
      $clanList = ClanHandler::getClansByUser($user);

      foreach ($clanList as $clan) {
        if (($clan->isQualified($clan->getCompo()) && $clan->getCompo()->getChat() == $chat)) {
          return true;
        }
      }
      // You can also read from a match you are a part of
    }
  }
}
?>
