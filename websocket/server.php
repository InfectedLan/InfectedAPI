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
/**
 * How the protocol works
 *
 * The protocol is a simple JSON-based packet protocol. Why JSON? It's simple to decode on the js client. Seriously.
 * Each packet consists of an object with two fields: intent, and data.
 * Intent is a string that describes what the packet represents. Think of it as a "packet type"
 * Data is ALLWAYS an array containing the data the packet wants to communicate.
 *
 * Why allways an array? This way, we are never confused as to what intent uses object, and what uses an array.
 */
/**
 * Client -> Server intents:
 *     auth - data[0] contains the current session id. This is used to identify the client to the server.
 *     subscribeChatroom - data[0] contains the chatroom the client wants to subscribe to.
 *     chatMessage - data[0] is the channel, data[1] is the message.
 *     unsubscribeChatroom - data[0] is the chatroom to unsubscribe from
 *
 * Server -> Client intents:
 *     authResult - data[0] containts the result of the authentication
 *     subscribeChatroomResult - data[0] contains if the task failed or not, data[1] contains if you can chat or not, data[2] contains the chat id, and data[3] contains a message(might be blank)
 *     chatMessageResult - data[0] is if the chatmessage was success or not, data[1] is the channel the message was sendt to, data[2] is either the new chat line(handle it same way as newChat[1], or an error message
 *     chat - data[0] is the channel, data[1]  is a formatted chat message
 *     unsubscribeChatroomResult - data[0] is if it was successful or not, data[1] is an error message(or an empty string if none)
 *
 * FAQ:
 *  * Why all the result packets? Why not reuse the name back?
 *     - Consistency.
 *
 */
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/test.infected.no/public_html/api');
set_time_limit(0); //Make sure the script runs forever

require_once 'session.php';
require_once 'handlers/chathandler.php';
require_once 'libraries/phpwebsockets/websockets.php';

class Server extends WebSocketServer {
  private $authenticatedUsers;
  private $followingChatChannels;

  function __construct($addr, $port, $bufferLength = 2048) {
    parent::__construct($addr, $port, $bufferLength);

    $this->authenticatedUsers = new SplObjectStorage();
    $this->followingChatChannels = array(); // Fun fact, PHP arrays are not arrays! It is if you do [] instead...
  }

	protected function process($session, $message) {
		//$this->send($session, "You sendt" . $message);
		echo $message . "\n";

    $parsedJson = json_decode($message);

    switch ($parsedJson->intent) {
      case 'auth':
        $user = Session::getUserFromSessionId($parsedJson->data[0]);

        if ($user != null) {
          $this->registerUser($user, $session);
          $this->send($session, '{"intent": "authResult", "data": [true]}');
        } else {
          echo "Disconnecting user due to failure to authenticate\n";

          $this->send($session, '{"intent": "authResult", "data": [false]}');
          $this->disconnect($session);
        }
        break;

      case 'subscribeChatroom':
        if ($this->isAuthenticated($session)) {
          $chat = ChatHandler::getChat($parsedJson->data[0]);

          if ($chat != null) {
            $this->subscribeChatroom($chat, $session);
          }
        } else {
          echo "Disconnecting user due to no authentication: " . $this->getUser($session)->getUsername() . "\n";

          $this->send($session, '{"intent": "subscribeChatroomResult", "data": [false, false, ' . $parsedJson->data[0] . ', "Du har ikke logget inn!"]}');
          $this->disconnect($session);
        }
        break;

      case 'chatMessage':
        if ($this->isAuthenticated($session)) {
          $this->sendChatMessage($session, $parsedJson->data[0], $parsedJson->data[1]);
        } else {
          echo "Disconnecting user due to no authentication: " . $this->getUser($session)->getUsername() . "\n";

          $this->send($session, '{"intent": "chatMessageResult", "data": [false, "Du er ikke authentisert!"]}');
          $this->disconnect($session);
        }
        break;

      case 'unsubscribeChatroom':
        if ($this->isAuthenticated($session)) {
          $this->unsubscribeChatroom($parsedJson->data[0], $session);
        } else {
          echo "Disconnecting user due to no authentication: " . $this->getUser($session)->getUsername() . "\n";

          $this->send($session, '{"intent": "unsubscribeChatroomResult", "data": [false, "Du er ikke authentisert!"]}');
          $this->disconnect($session);
        }
        break;

      default:
        echo 'Got unhandled intent: ' . $parsedJson->intent . '\n';
		}
	}


	protected function connected($session) {
		echo "Got connection\n";
	}

	protected function closed($session) {
		echo "Lost connection\n";

    foreach ($this->followingChatChannels as &$chatroom) {
      if (($key = array_search($session, $chatroom)) !== false) {
        echo "Removing from one chatroom\n";

        unset($chatroom[$key]);
      }
    }

    $this->unregisterUser($session);
	}

  protected function unsubscribeChatroom($channel, $session) {
    if (isset($this->followingChatChannels[$channel])) {
      if (($key = array_search($session, $this->followingChatChannels[$channel])) !== false) {
          unset($this->followingChatChannels[$channel][$key]);
          $this->send($session, '{"intent": "unsubscribeChatroomResult", "data": [true, ""]}');
      } else {
          $this->send($session, '{"intent": "unsubscribeChatroomResult", "data": [false, "Du prøvde å forlate et chatrom du aldri var i!"]}');
      }
    } else {
        $this->send($session, '{"intent": "unsubscribeChatroomResult", "data": [false, "Du prøvde å forlate et chatrom du aldri var i!"]}');
    }
  }

  protected function sendChatMessage($session, $channel, $message) {
    $chat = ChatHandler::getChat($channel);
    $user = $this->getUser($session);

    if ($chat != null) {
      if (ChatHandler::canChat($chat, $user)) {
          //We can chat here. Let's
          $line = $this->getFormattedChatMessage($user, $message, time());
          $this->send($session, '{"intent": "chatMessageResult", "data": [true, ' . $channel . ', "' . $line . '"]}');
          //Next, broadcast to all people following the chat
          foreach ($this->followingChatChannels[$channel] as $victim) {
            if ($victim != $session) {
              $this->send($session, '{"intent": "chat", "data": [' . $channel . ', "' . $line . '"]}');
            }
          }
        $chat->sendMessage($user, $message);
      } else {
        $this->send($session, '{"intent": "chatMessageResult", "data": [false, ' . $channel . ', "Du kan ikke chatte her!"]}');
      }
    } else {
      $this->send($session, '{"intent": "chatMessageResult", "data": [false, ' . $channel . ', "Chatten finnes ikke!"]}');
    }
  }

  protected function getFormattedChatMessage($user, $message, $timestamp) {
    $time = date('H:i:s', $timestamp);
    $username = ($user->hasPermission('*') || $user->hasPermission('compo.chat') ? "<b>[Admin] " . $user->getUsername() . "</b>" : $user->getUsername());
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    return $time . $username . ": " . $message;
  }

  protected function subscribeChatroom($chat, $session) {
    if (ChatHandler::canRead($chat, $this->getUser($session))) {
      if (!isset($this->followingChatChannels[$chat->getId()])) {
        $this->followingChatChannels[$chat->getId()] = array();
      }

      $this->followingChatChannels[$chat->getId()][] = $session; //Add thge user to list of people who will recieve updates when something changes
      $this->send($session, '{"intent": "subscribeChatroomResult", "data": [true, ' . ChatHandler::canChat($chat, $this->getUser($session)) . ', ' . $chat->getId() . ', ""]}');

      //Get the latest messages, and send them!
      $messages = $chat->getLastMessages(30);
      $messages = array_reverse($messages);

      foreach ($messages as $message) {
        $text = $this->getFormattedChatMessage($message->getUser(), $message->getMessage(), $message->getTime());
        //Send as a chat for now
        $this->send($session, '{"intent": "chat", "data": [' . $chat->getId() . ', "' . $text . '"]}');
      }
    } else {
      $this->send($session, '{"intent": "subscribeChatroomResult", "data": [false, false, ' . $chat->getId() . ' , "Du har ikke tillatelse til å bruke dette chatrommet"]}'); // TODO add locale
    }
  }

  protected function unregisterUser($session) {
    unset($this->authenticatedUsers[$session]);
  }

  protected function registerUser($user, $session){
    echo "Got user: " . $user->getUsername() . ".\n";

    $this->authenticatedUsers[$session] = $user;
  }

  protected function isAuthenticated($session) {
    return $this->authenticatedUsers[$session] != null;
  }

  protected function getUser($session) {
    return $this->authenticatedUsers[$session];
  }

  protected function tick() {

  }
}

$server = new Server("0.0.0.0", "1337");
echo "Whoami: " . exec("whoami") . "\n";
//phpinfo();
echo "\n";

try {
	$server->run();
} catch(Exception $exception) {
	$server->stdout($exception->getMessage());
}
?>
