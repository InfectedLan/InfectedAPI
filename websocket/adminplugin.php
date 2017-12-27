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
require_once 'phpwebsockets/users.php';
require_once 'handlers/chathandler.php';
require_once 'websocketplugin.php';

class AdminPlugin extends WebSocketPlugin {
  private $subscribedPeople;
  private $server;

  function __construct(Server $server) {
    parent::__construct($server);
    println("Booting up admin plugin");
    //$server->registerIntent("subscribeLog", $this);
    $server->registerIntent("command", $this);
    //$server->registerIntent("unsubscribeLog", $this);
    $server->registerPlugin($this, "AdminPlugin");

    $this->server = $server;

    $this->subscribedPeople = array(); // Fun fact, PHP arrays are not arrays! It is if you do [] instead...
  }

  public function log($string) {
  	foreach($this->subscribedPeople as $connection) {
  	    $this->server->send($connection, '{"intent": "commandResponse", "data": ["' + htmlspecialchars($message, ENT_QUOTES, 'UTF-8') + '"]}');
  	}
  }

  public function handleIntent($intent, $args, WebSocketUser $connection) {
  	if (!$this->server->isAuthenticated($connection)) {
	    println("User tried using admin plugin without auth!");
	    $this->server->send($connection, '{"intent": "error", "data": ["Du har ikke logget inn!"]}');
	    $this->server->disconnect($connection);
	    return;
  	}

    $user = $this->server->getUser($connection);

    if (!$user->hasPermission('admin.websocket')) {
	    println("User did not have permissions to run an admin plugin intent: " . $user->getId());
	    $this->server->send($connection, '{"intent": "error", "data": ["Du prøvde å bruke admin-komponenten uten tillatelse!"]}');
	    $this->server->disconnect($connection);
  	}

    switch($intent) {/*
      case 'subscribeLog':
    $this->subscribedPeople[] = $connection;
          break;
		 */
      case 'command':
        $this->handleCommand($connection, $args);
        break;
    /*
      case 'unsubscribeChatroom':
    if (($key = array_search($connection, $this->subscribedPeople)) !== false) {
              println("Unsubscribing connection from admin messages");
              unset($this->subscribedPeople[$key]);
          }
          break;
    */
      }
  }

  public function onConnect($connection) {

  }

  public function onDisconnect(WebSocketUser $connection) {
  	if (($key = array_search($connection, $this->subscribedPeople)) !== false) {
	    println("Unsubscribing connection from admin messages");
	    unset($this->subscribedPeople[$key]);
  	}
  }

  protected function handleCommand(WebSocketUser $connection, $args) {
  	switch (strtolower($args[0])) {
    	case 'help':
  	    $this->server->send($connection, '{"intent": "commandResponse", "data": ["Help for the websocket admin-ui", "", "&nbsp;help: shows this list", "&nbsp;refreshAll: Force-refreshes all connected clients within the next 60 seconds(plus 10 sec warning)", "&nbsp;online: Shows all authenticated people", "&nbsp;subscribeLog: Prints happenings to this console.", "&nbsp;unsubscribeLog: Unsubscribes you from recieving log info in this console", "&nbsp;plugins: Lists loaded plugins", "&nbsp;chatroomSubscriptions: Shows who is subscribed to what chatroom"]}');
  	    break;

    	case 'refreshall':
  	    foreach ($this->server->authenticatedUsers as $person) {
      		$delay = rand(10*1000, 60*1000);
      		$this->server->send($person, '{"intent": "refresh", "data": [' . $delay . ']}');
      		$this->server->disconnect($connection);
  	    }

  	    $this->server->send($connection, '{"intent": "commandResponse", "data": ["See you in a bit!"]}');
  	    break;

    	case 'online':
  	    $data = [""];
  	    $count = 0;
  	    $index = 0;

    	  foreach ($this->server->authenticatedUsers as $person) {
      		$user = $this->server->getUser($person);
      		$data[$index] = $data[$index] ."&nbsp;" . $user->getUsername() . '(' . $user->getId() . ')';

          if ($count % 4 == 3) {
      		    $index++;
      		}

          $count++;
  	    }

  	    $data[] = $count . " people total.";
  	    $message = ["intent" => "commandResponse", "data" => $data];
  	    $this->server->send($connection, json_encode($message));
        break;

    	case 'subscribelog':
  	    $this->subscribedPeople[] = $connection;
  	    break;

    	case 'unsubscribelog':
  	    if (($key = array_search($connection, $this->subscribedPeople)) !== false) {
                  println("Unsubscribing connection from admin messages");
                  unset($this->subscribedPeople[$key]);
        }
        break;

    	case 'plugins':
  	    $pluginList = [];
  	    $pluginList[] = "Loaded plugins: " . count($this->server->plugins);

        foreach($this->server->plugins as $key => $val) {
  		    $pluginList[] = " * " . $key;
  	    }

  	    $jsonData = ["intent"=>"commandResponse", "data" => $pluginList];
  	    $this->server->send($connection, json_encode($jsonData));
  	    break;

    	case 'chatroomsubscriptions':
  	    $lines = [];
  	    $lines[] = "Chatrooms:";

  	    foreach ($this->server->plugins["ChatPlugin"]->followingChatChannels as $key => $value) {
      		$room = ChatHandler::getChat($key);
      		$lines[] = "---" . $room->getTitle() . "(" . $key . ")---";
      		$lines[] = "Subscribers: " . count($value);
      		$lines[] = "";
      		$count = 0;
      		$index = count($lines)-1;

          foreach($value as $person) {
    		    $user = $this->server->getUser($person);
    		    $lines[$index] = $lines[$index] . "&nbsp;" . $user->getUsername() . '(' . $user->getId() . ')';

            if ($count % 4 == 3) {
    			    $index++;
    		    }

    		    $count++;
      		}
  	    }

  	    $jsonData = ["intent"=>"commandResponse", "data" => $lines];
  	    $this->server->send($connection, json_encode($jsonData));
        break;

      default:
  	    $this->server->send($connection, '{"intent": "commandResponse", "data": ["Unknown command - type help for help."]}');
  	    break;
    }
  }
}
?>
