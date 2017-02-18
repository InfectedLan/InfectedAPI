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
require_once 'server.php';
require_once 'websocketplugin.php';
require_once 'handlers/chathandler.php';

class ChatPlugin extends WebSocketPlugin {
    public $followingChatChannels;
    private $server;
        
    function __construct(Server $server) {
        parent::__construct($server);
        $server->registerIntent("subscribeChatroom", $this);
        $server->registerIntent("chatMessage", $this);
        $server->registerIntent("unsubscribeChatroom", $this);
        $server->registerPlugin($this, "ChatPlugin");

        $this->server = $server;

        $this->followingChatChannels = array(); // Fun fact, PHP arrays are not arrays! It is if you do [] instead...
    }
    
    public function handleIntent($intent, $args, $connection) {
        switch($intent) {
        case 'subscribeChatroom':
            if ($this->server->isAuthenticated($connection)) {
                $chat = ChatHandler::getChat($args[0]);

                if ($chat != null) {
                    $this->subscribeChatroom($chat, $connection);
                }
            } else {
                echo "Disconnecting user due to no authentication: " . $this->server->getUser($connection)->getUsername() . "\n";

                $this->server->send($connection, '{"intent": "subscribeChatroomResult", "data": [false, false, ' . $parsedJson->data[0] . ', "Du har ikke logget inn!"]}');
                $this->server->disconnect($connection);
            }
            break;

        case 'chatMessage':
            if ($this->server->isAuthenticated($connection)) {
                $this->sendChatMessage($connection, $args[0], urldecode($args[1]));
            } else {
                echo "Disconnecting user due to no authentication: " . $this->server->getUser($connection)->getUsername() . "\n";

                $this->server->send($connection, '{"intent": "chatMessageResult", "data": [false, "Du er ikke authentisert!"]}');
                $this->server->disconnect($connection);
            }
            break;

        case 'unsubscribeChatroom':
            if ($this->server->isAuthenticated($connection)) {
                $this->unsubscribeChatroom($args[0], $connection);
            } else {
                echo "Disconnecting user due to no authentication: " . $this->server->getUser($connection)->getUsername() . "\n";

                $this->server->send($connection, '{"intent": "unsubscribeChatroomResult", "data": [false, "Du er ikke authentisert!"]}');
                $this->server->disconnect($connection);
            }
            break;

        }
    }

    public function onConnect($connection) {

    }

    public function onDisconnect($connection) {
	echo "Handling chat disconnect\n";
        foreach ($this->followingChatChannels as $chatKey => $chatroom) {
	    foreach($chatroom as $key => $value) {
		if($connection == $value) {
		    echo "Removing from one chatroom\n";		    
		    unset($this->followingChatChannels[$chatKey][$key]);
		}
	    }
        }
    }
    
    protected function unsubscribeChatroom($channel, $connection) {
        if (isset($this->followingChatChannels[$channel])) {
            if (($key = array_search($connection, $this->followingChatChannels[$channel])) !== false) {
                unset($this->followingChatChannels[$channel][$key]);
                $this->server->send($connection, '{"intent": "unsubscribeChatroomResult", "data": [true, ""]}');
            } else {
                $this->server->send($connection, '{"intent": "unsubscribeChatroomResult", "data": [false, "Du prøvde å forlate et chatrom du aldri var i!"]}');
            }
        } else {
            $this->server->send($connection, '{"intent": "unsubscribeChatroomResult", "data": [false, "Du prøvde å forlate et chatrom du aldri var i!"]}');
        }
    }

    protected function sendChatMessage($connection, $channel, $message) {
        $chat = ChatHandler::getChat($channel);
        $user = $this->server->getUser($connection);
        echo "User " . $user->getId() . " chatted " . $message . "\n";

        if ($chat != null) {
            if (ChatHandler::canChat($chat, $user)) {
                //We can chat here. Let's
                $line = $this->getFormattedChatMessage($user, $message, time());
                $this->server->send($connection, '{"intent": "chatMessageResult", "data": [true, ' . $channel . ', "' . $line . '"]}');
                //Next, broadcast to all people following the chat
                foreach ($this->followingChatChannels[$channel] as $victim) {
                    if ($victim != $connection) {
                        $this->server->send($victim, '{"intent": "chat", "data": [' . $channel . ', "' . $line . '"]}');
                    }
                }
                $chat->sendMessage($user, $message);
            } else {
                $this->server->send($connection, '{"intent": "chatMessageResult", "data": [false, ' . $channel . ', "Du kan ikke chatte her!"]}');
            }
        } else {
            $this->server->send($connection, '{"intent": "chatMessageResult", "data": [false, ' . $channel . ', "Chatten finnes ikke!"]}');
        }
    }

    protected function getFormattedChatMessage($user, $message, $timestamp) {
        $time = date('H:i', $timestamp);
        $username = ($user->hasPermission('*') || $user->hasPermission('compo.chat') ? "<b>[Admin] " . $user->getUsername() . "</b>" : $user->getUsername());
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        return $time . ' ' . $username . ": " . $message;
    }

    protected function subscribeChatroom($chat, $connection) {
        if (ChatHandler::canRead($chat, $this->server->getUser($connection))) {
            if (!isset($this->followingChatChannels[$chat->getId()])) {
                $this->followingChatChannels[$chat->getId()] = array();
            }

            $this->followingChatChannels[$chat->getId()][] = $connection; //Add thge user to list of people who will recieve updates when something changes
            $this->server->send($connection, '{"intent": "subscribeChatroomResult", "data": [true, ' . ChatHandler::canChat($chat, $this->server->getUser($connection)) . ', ' . $chat->getId() . ', ""]}');

            //Get the latest messages, and send them!
            $messages = $chat->getLastMessages(30);
            $messages = array_reverse($messages);

            foreach ($messages as $message) {
                $text = $this->getFormattedChatMessage($message->getUser(), $message->getMessage(), $message->getTime());
                //Send as a chat for now
                $this->server->send($connection, '{"intent": "chat", "data": [' . $chat->getId() . ', "' . $text . '"]}');
            }
        } else {
            $this->server->send($connection, '{"intent": "subscribeChatroomResult", "data": [false, false, ' . $chat->getId() . ' , "Du har ikke tillatelse til å bruke dette chatrommet"]}'); // TODO add locale
        }
    }

}

?>