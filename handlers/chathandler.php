<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/chat.php';
require_once 'objects/chatmessage.php';
require_once 'objects/clan.php';
require_once 'objects/user.php';

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
 	 * Adds entire clan to chat
 	 */
	public static function addClanMembersToChat(Chat $chat, Clan $clan) {
		foreach ($clan->getMembers() as $member) {
			self::addChatMember($member, $chat);
		}
    }
	
	/*
	 * Return all chats.
	 */
	public static function getChats() {
		$database = Database::open(Settings::db_name_infected_compo);
        
        $result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_chats . '`;');
                                      
        $database->close();
        
		$chatList = array();
		
        while ($object = $result->fetch_object('Chat')) {
            array_push($chatList, $object);
        }

        return $chatList;
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
        
		$chatMessageList = array();
		
        while ($object = $result->fetch_object('ChatMessage')) {
            array_push($chatMessageList, $object);
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
	public static function getLastMessages(Chat $chat, $count) {
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
	 * Creates a new chat and returns the object.
	 */
	public static function createChat($name) {
		$database = Database::open(Settings::db_name_infected_compo);
		
		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_chats . '` (`name`) 
                          VALUES (\'' . $database->real_escape_string($name) . '\');');
			
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
	 * Remove chat messages for given chat.
	 */
	public static function removeChatMessages(Chat $chat) {
		$database = Database::open(Settings::db_name_infected_compo);
		
		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_chatmessages . '` 
                          WHERE `chatId` = \'' . $chat->getId() . '\';');
            
        $database->close();
	}
	
	/*
	 * Returns true if the given user is member of the given chat.
	 */
	public static function isChatMember(User $user, Chat $chat) {
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
		
		$chatMemberList = array();

        while ($object = $result->fetch_object('User')) {
            array_push($chatMemberList, $object);
        }

        return $chatMemberList;
	}
	
	/*
	 * Add the given user to the specified chat.
	 */
	public static function addChatMember(User $user, Chat $chat) {
		$database = Database::open(Settings::db_name_infected_compo);
		
		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberofchat . '` (`userId`, `chatId`) 
                          VALUES (\'' . $user->getId() . '\',
							      \'' . $chat->getId() . '\');');
						
		$database->close();
	}
	
	/*
	 * Remove the given user from the specified chat.
	 */
	public static function removeChatMember(User $user, Chat $chat) {
		$database = Database::open(Settings::db_name_infected_compo);
		
		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_memberofchat . '` 
                          WHERE `userId` = \'' . $user->getId() . '\'
					      AND `chatId` = \'' . $chat->getId() . '\';');
            
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
	 * Send a massage to this chat from the given user.
	 */
	public static function sendChatMessage(User $user, Chat $chat, $message) {
		$database = Database::open(Settings::db_name_infected_compo);
		
		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_chatmessages . '` (`userId`, `chatId`, `time`, `message`) 
                          VALUES (\'' . $user->getId() . '\',
							      \'' . $chat->getId() . '\',
							      \'' . date('Y-m-d H:i:s') . '\',
							      \'' . htmlspecialchars($database->real_escape_string($message), ENT_QUOTES | ENT_HTML401) . '\');');
		
		$database->close();
	}
}
?>