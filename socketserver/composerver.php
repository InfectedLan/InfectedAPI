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
set_include_path(get_include_path() . PATH_SEPARATOR . '/home/test.infected.no/public_html/api');
set_time_limit(0); //Make sure the script runs forever

require_once 'libraries/phpwebsockets/websockets.php';
require_once 'authenticateduser.php';

class CompoServer extends WebSocketServer {
	protected function process($session, $message) {
		//$this->send($session, "You sendt" . $message);
		echo $message . "\n";
		$parsedJson = json_decode($message);
		switch ($parsedJson->intent) {
			case 'auth':
				AuthenticatedUser::authenticateUser($session, $parsedJson->data[0]);
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
