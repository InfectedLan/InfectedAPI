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
 *     subscribeMatches - tells the server that we want to get updates on matches for the logged in user
 *
 * Server -> Client intents:
 *     authResult - data[0] containts the result of the authentication
 *     subscribeChatroomResult - data[0] contains if the task failed or not, data[1] contains if you can chat or not, data[2] contains the chat id, and data[3] contains a message(might be blank)
 *     chatMessageResult - data[0] is if the chatmessage was success or not, data[1] is the channel the message was sendt to, data[2] is either the new chat line(handle it same way as newChat[1], or an error message
 *     chat - data[0] is the channel, data[1]  is a formatted chat message
 *     unsubscribeChatroomResult - data[0] is if it was successful or not, data[1] is an error message(or an empty string if none)
 *     matchUpdate - updates the client about the users current match. data[0] is an object with information on the match
 *     error - data[0] is a general purpose error message
 *
 * FAQ:
 *  * Why all the result packets? Why not reuse the name back?
 *     - Consistency.
 *  * These plugin thingies... How do i check if the user has authenticated?
 *     - We are using a model where we expect the user to be authenticated to be able to use any intents other then the "authenticate" one. Therefore, intent handlers are to be coded naive of weither we are logged in or not.
 *
 */
require_once '../settings.php';
set_include_path(get_include_path() . PATH_SEPARATOR . substr(Settings::api_path, 0, strlen(Settings::api_path)-1));
echo "Set include path to " . get_include_path() . "\n";
set_time_limit(0); //Make sure the script runs forever

require_once 'session.php';
require_once 'libraries/phpwebsockets/websockets.php';
require_once 'libraries/phpwebsockets/users.php';

require_once 'chatplugin.php';
require_once 'matchplugin.php';

class Server extends WebSocketServer {
  private $authenticatedUsers;
  private $intentHandlers;
  private $plugins;

  function __construct($addr, $port, $bufferLength = 2048) {
    parent::__construct($addr, $port, $bufferLength);

    $this->authenticatedUsers = new SplObjectStorage();
    $this->intentHandlers = array();
    $this->plugins = array();
  }

  protected function process(WebSocketUser $connection, $message) {
    //$this->send($connection, "You sendt" . $message);
    echo $message . "\n";

    $parsedJson = json_decode($message);

    switch ($parsedJson->intent) {
    case 'auth':
      $user = Session::getUserFromSessionId($parsedJson->data[0]);

      if ($user != null) {
	$this->registerUser($user, $connection);
	$this->send($connection, '{"intent": "authResult", "data": [true]}');
	echo "Authenticated user: " . $user->getId() . "\n";
      } else {
	echo "Disconnecting user due to failure to authenticate\n";

	$this->send($connection, '{"intent": "authResult", "data": [false]}');
	$this->disconnect($connection);
      }
      break;

    default:
      if(isset($this->intentHandlers[$parsedJson->intent])) {
	$this->intentHandlers[$parsedJson]->handleIntent($parsedJson->intent, $parsedJson->data);
      } else {
	echo 'Got unhandled intent: ' . $parsedJson->intent . '\n';
      }
    }
  }

  public function registerIntent($intent, $handler) {
    $intentHandlers[$intent] = $handler;
  }

  public function registerPlugin($plugin) {
    array_push($this->plugins, $plugin);
  }


  protected function connected(WebSocketUser $connection) {
    echo "Got connection\n";
    foreach($this->plugins as $plugin) {
      $plugin->onConnect($connection);
    }
  }

  protected function closed(WebSocketUser $connection) {
    echo "Lost connection\n";

    foreach($this->plugins as $plugin) {
      $plugin->onConnect($connection);
    }

    $this->unregisterUser($connection);
  }
    
  protected function unregisterUser(WebSocketUser $connection) {
    unset($this->authenticatedUsers[$connection]);
  }

  protected function registerUser(User $user, WebSocketUser $connection){
    echo "Got user: " . $user->getUsername() . ".\n";

    $this->authenticatedUsers[$connection] = $user;
  }

  public function isAuthenticated(WebSocketUser $connection) {
    return $this->authenticatedUsers[$connection] != null;
  }

  public function getUser(WebSocketUser $connecton) {
    return $this->authenticatedUsers[$connection];
  }

  protected function tick() {
    foreach($this->plugins as $plugin) {
      $plugin->tick();
    }
  }
}

//Command line helpers
function println($string) {
    echo $string . "\n";
}
function readln() {
    return fgetc(STDIN);
}

$server = new Server("0.0.0.0", "1337");
//Plugins
$chatPlugin = new ChatPlugin($server);
$matchPlugin = new MatchPlugin($server);

//Print some information to debug sanity of the server
echo "Whoami: " . exec("whoami") . "\n";
echo "Session store: " . session_save_path() . "\n";
//phpinfo();
echo "\n";

try {
	$server->run();
} catch(Exception $exception) {
	$server->stdout($exception->getMessage());
}
?>
