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
 *
 * Server -> Client intents:
 *     authResult - data[0] containts the result of the authentication
 *     subscribeChatroomResult - data[0] contains if the task failed or not, data[1] contains a message(might be blank)
 */
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/test.infected.no/public_html/api');
set_time_limit(0); //Make sure the script runs forever

require_once '../libraries/phpwebsockets/websockets.php';
require_once 'session.php';
set_time_limit(0); //Make sure the script runs forever

class CompoServer extends WebSocketServer {
    private $authenticatedUsers;
    private $followingChatChannels;
    
	protected function process($session, $message) {
        $this->authenticatedUsers = new SplObjectStorage();
        $this->followingChatChannels = array(); //Fun fact, PHP arrays are not arrays! 
		//$this->send($session, "You sendt" . $message);
		echo $message . "\n";
		$parsedJson = json_decode($message);
		switch ($parsedJson->intent) {
        case 'auth':
            $user = Session::getUserFromSessionId($parsedJson->data[0]);
            if($user != null) {
                $this->registerUser($user, $session);
                $this->send($session, '{"intent": "authResult", "data": [true]}');
            } else {
                $this->send($session, '{"intent": "authResult", "data": [false]}');
            }
            break;
        case 'subscribeChatroom':
            if($this->isAuthenticated($session)) {

            } else {
                $this->send($session, '{"intent": "subscribeChatroomResult", "data": [false, "Du har ikke logget inn!"]}');
            }
            break;

        default:
            echo "Got unhandled intent: " . $parsedJson->intent . "\n";
		}
	}

	protected function connected($session) {
		echo "Got connection\n";
	}

	protected function closed($session) {
		echo "Lost connection\n";
	}

    protected function registerUser($user, $session){
        echo "Got user: " . $user->getUsername() . ".\n";
        $this->authenticatedUsers[$session] = $user;
        print_r($this->authenticatedUsers);
    }

    protected function isAuthenticated($session) {
        return $this->authenticatedUsers[$session] != null;
    }

    protected function getUser($session) {
        return $this->authenticatedUsers($session);
    }

    protected function tick() {

    }
}

$server = new CompoServer("0.0.0.0", "1337");
echo "Whoami: " . exec("whoami") . "\n";
//phpinfo();
echo "\n";

try {
	$server->run();
} catch(Exception $e) {
	$server->stdout($e->getMessage());
}
?>
