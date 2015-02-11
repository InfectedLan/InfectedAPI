<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/chat.php';
require_once 'objects/chatmessage.php';

class ChatHandler {	
	/*
	 * Return the chat with the given id.
	 */
	public static function getChat($id) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_chats . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                      
        $mysql->close();
		
		return $result->fetch_object('Chat');
	}
	
	/*
	 * Return all chats.
	 */
	public static function getChats() {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_chats . '`;');
                                      
        $mysql->close();
        
		$chatList = array();
		
        while ($row = $result->fetch_array()) {
            array_push($chatList, self::getChat($row['id']));
        }

        return $chatList;
	}
	
	/*
	 * Return the chat message with the given id.
	 */
	public static function getChatMessage($id) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_chatmessages . '` 
                                 WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                      
        $mysql->close();
        
		$row = $result->fetch_array();
		
        if ($row) {
            return new ChatMessage($row['id']);
        }
	}
	
	/*
	 * Return the chat message with the given id.
	 */
	public static function getChatMessages() {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_chatmessages . '`;');
                                      
        $mysql->close();
        
		$chatMessageList = array();
		
        while ($row = $result->fetch_array()) {
            array_push($chatMessageList, self::getChatMessage($row['id']));
        }

        return $chatMessageList;
	}
	
	/*
	 * Returns the last chat messages for the given chat.
	 */
	public static function getLastChatMessage($chat) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_chatmessages . '`
                                 WHERE `chatId` = \'' . $mysql->real_escape_string($chat->getId()) . '\'
                                 ORDER BY `id` ASC
								 LIMIT ' . $count . ';');
        
		$mysql->close();
		
		$row = $result->fetch_array();
		
		if ($row) {
			return self::getChatMessage($row['id']);
		}
	}
	
	/*
	 * Returns an array of the last given number of chat messages for given chat.
	 */
	public static function getLastMessages($chat, $count) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_chatmessages . '`
                                 WHERE `chatId` = \'' . $mysql->real_escape_string($chat->getId()) . '\'
                                 ORDER BY `id` ASC
								 LIMIT ' . $count . ';');
        
		$mysql->close();
		
		$chatMessageList = array();
		
        while ($row = $result->fetch_array()) {
            array_push($chatMessageList, self::getChatMessage($row['id']));
        }

        return $chatMessageList;
	}
	
	/*
	 * Creates a new chat and returns the object.
	 */
	public static function createChat($name) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_chats . '` (`name`) 
                       VALUES (\'' . $mysql->real_escape_string($name) . '\');');
						
		$chat = self::getChat($mysql->insert_id);
						
		$mysql->close();
		
		return $chat;
	}
	
	/*
	 * Remove a chat.
	 */
	public static function removeChat($chat) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$mysql->query('DELETE FROM `' . Settings::db_table_infected_compo_chats . '` 
                       WHERE `id` = \'' . $mysql->real_escape_string($chat->getId()) . '\';');
            
        $mysql->close();
		
		// Remove all chat messages for this chat.
		self::removeChatMessages($chat);
		
		// Remove all members from this chat.
		self::removeChatMembers($chat);
	}
	
	/*
	 * Remove chat messages for given chat.
	 */
	public static function removeChatMessages($chat) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$mysql->query('DELETE FROM `' . Settings::db_table_infected_compo_chatmessages . '` 
                       WHERE `chatId` = \'' . $mysql->real_escape_string($chat->getId()) . '\';');
            
        $mysql->close();
	}
	
	/*
	 * Returns true if the given user is member of the given chat.
	 */
	public static function isChatMember($user, $chat) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_memberofchat . '`
                                 WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
								 AND `chatId` = \'' . $mysql->real_escape_string($chat->getId()) . '\';');
        
		$mysql->close();
		
        $row = $result->fetch_array();

        return $row ? true : false;
	}
	
		
	/*
	 * Returns an array of all members in the specificed chat.
	 */
	public static function getChatMembers($chat) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$result = $mysql->query('SELECT `userId` FROM `' . Settings::db_table_infected_compo_memberofchat . '`
                                 WHERE `chatId` = \'' . $mysql->real_escape_string($chat->getId()) . '\';');
        
		$mysql->close();
		
		$chatMemberList = array();
		
        while ($row = $result->fetch_array()) {
            array_push($chatMemberList, UserHandler::getUser($row['id']));
        }

        return $chatMemberList;
	}
	
	/*
	 * Add the given user to the specified chat.
	 */
	public static function addChatMember($user, $chat) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_memberofchat . '` (`userId`, `chatId`) 
                       VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\',
							   \'' . $mysql->real_escape_string($chat->getId()) . '\');');
						
		$mysql->close();
	}
	
	/*
	 * Remove the given user from the specified chat.
	 */
	public static function removeChatMember($user, $chat) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$mysql->query('DELETE FROM `' . Settings::db_table_infected_compo_memberofchat . '` 
                       WHERE `userId` = \'' . $mysql->real_escape_string($user->getId()) . '\'
					   AND `chatId` = \'' . $mysql->real_escape_string($chat->getId()) . '\';');
            
        $mysql->close();
	}
	
	/*
	 * Remove members from the given chat.
	 */
	public static function removeChatMembers($chat) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$mysql->query('DELETE FROM `' . Settings::db_table_infected_compo_memberofchat . '` 
                       WHERE `chatId` = \'' . $mysql->real_escape_string($chat->getId()) . '\';');
            
        $mysql->close();
	}
	
	/*
	 * Send a massage to this chat from the given user.
	 */
	public static function sendChatMessage($user, $chat, $message) {
		$mysql = MySQL::open(Settings::db_name_infected_compo);
		
		$mysql->query('INSERT INTO `' . Settings::db_table_infected_compo_chatmessages . '` (`userId`, `chatId`, `message`) 
                       VALUES (\'' . $mysql->real_escape_string($user->getId()) . '\',
							   \'' . $mysql->real_escape_string($chat->getId()) . '\',
							   \'' . $mysql->real_escape_string($message) . '\');');
						
		$mysql->close();
	}
}
?>