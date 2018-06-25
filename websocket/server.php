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
set_include_path(get_include_path() . PATH_SEPARATOR . substr(Settings::getValue("api_path"), 0, strlen(Settings::getValue("api_path"))-1));
echo "Set include path to " . get_include_path() . "\n";
set_time_limit(0); //Make sure the script runs forever

require_once 'session.php';
require_once 'phpwebsockets/websockets.php';
require_once 'phpwebsockets/users.php';

require_once 'chatplugin.php';
require_once 'matchplugin.php';
require_once 'adminplugin.php';

class Server extends WebSocketServer {
  public $authenticatedUsers;
  public $plugins;

  private $discordKey;
  private $intentHandlers;

  function __construct($addr, $port, $discordKey, $bufferLength = 2048) {
    parent::__construct($addr, $port, $bufferLength);

    $this->authenticatedUsers = new SplObjectStorage();
    $this->intentHandlers = array();
    $this->plugins = array();
    $this->discordKey = $discordKey;
  }

  protected function process(WebSocketUser $connection, $message) {
      //$this->send($connection, "You sendt" . $message);
      echo "INCOMING: " . $message . "\n";

      try {
          $parsedJson = json_decode($message);
          if(empty($message) || !isset($message)) {
              echo "Got empty packet!?!\n";
              return;
          }
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
              case 'keepAlive':

                  break;
              default:
                  if(isset($this->intentHandlers[$parsedJson->intent])) {

                      $this->intentHandlers[$parsedJson->intent]->handleIntent($parsedJson->intent, $parsedJson->data, $connection);
                  } else {
                      echo 'Got unhandled intent: ' . $parsedJson->intent . "\n";
                  }

                  break;
          }

      } catch(Exception $e) {
          echo 'Exception happened while procssing packet...\n';
          $this->reportToDiscord("New websocket message", "An exception ocurred");
      }
  }

  private function reportToDiscord(string $title, string $message, array $args = []) {
      $payload = ["username" => "Loggine-websocket",
          "icon_emoji" => ":warning:",
          "tts" => false,
          "embed" => [
              "title" => $title,
              "description" => $message,
              "fields" => $args
          ]
          ];
      $strPayload = json_encode($payload);
      $ch = curl_init($this->discordKey);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $strPayload);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
              'Content-Length: ' . strlen($strPayload))
      );

      $result = curl_exec($ch);
  }

  public function registerIntent($intent, $handler) {
    $this->intentHandlers[$intent] = $handler;
  }

  public function registerPlugin($plugin, $name) {
      $this->plugins[$name] = $plugin;
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
      $plugin->onDisconnect($connection);
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

  public function getUser(WebSocketUser $connection) {
    return $this->authenticatedUsers[$connection];
  }

  protected function tick() {
      $start = microtime(true);
      foreach($this->plugins as $plugin) {
	  $plugin->tick();
      }
      $stop = microtime(true);
      $diff = ($stop-$start)/1000;
      if($diff>100) {
	  echo "WARNING: tick() is taking more than 100ms\n";
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

if(count($argv) != 2 ) {
    echo 'Lacks discord key \n';
    die();
}

$server = new Server("0.0.0.0", "1337", $argv[1]);
//Plugins
$adminPlugin = new AdminPlugin($server);
$chatPlugin = new ChatPlugin($server);
$matchPlugin = new MatchPlugin($server);

//Same as println, but should be used for more important stuff.
/*
function logAdmin($string) {
    $adminPlugin->log($string);
    println($string);
}
*/

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
