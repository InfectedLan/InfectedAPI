<?php
include 'database.php';
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

require_once 'database.php';
require_once 'secret.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/sysloghandler.php';
require_once 'handlers/userhandler.php';

$reply = null;
$message = null;

if (isset($_GET['key']) &&
	!empty($_GET['key'])) {
	$key = $_GET['key'];

	// Checking for valid API key.
	if ($key == Secret::api_key) {
		if (isset($_GET['action']) &&
			!empty($_GET['action'])) {
			$action = $_GET['action'];

			if (isset($_GET['username']) &&
				!empty($_GET['username'])) {
				$username = $_GET['username'];

				if (UserHandler::hasUser($username)) {
					$user = UserHandler::getUserByIdentifier($username);
					$event = EventHandler::getCurrentEvent();

					switch ($action) {
						case "authorize":
							if (isset($_GET['password']) &&
								!empty($_GET['password'])) {
								$password = $_GET['password'];
								$hashedPassword = hash('sha256', $_GET['password']);
								$storedPassword = hash('sha256', "test"); //$user->getPassword();

								if ($user->isActivated()) {
									if (hash_equals($hashedPassword, $storedPassword)) {
										if (isAllowedToAuthenticate($user)) {
											$reply = ['control:SHA2-Password' => $hashedPassword];
											$message = 'User succesfully logged in.';

											//SyslogHandler::log("User logged in", "radius", $user);
										} else {
											$message = 'You\'re not allowed to use this service.';
										}
									} else {
										$message = Localization::getLocale('wrong_username_or_password');
									}
								} else {
									$message = Localization::getLocale('you_must_activate_your_user_account_in_order_to_logg_in');
								}
							}
						break;

				    case "post-auth":
							if (isset($_GET['port-type']) &&
								!empty($_GET['port-type'])) {
								$portType = $_GET['port-type'];

								// Default VLAN for authorized users.
								$vlan = 0;

								switch ($portType) {
									// Method: Ethernet
									// NAS-Port-Type = Ethernet
									case "Ethernet":
										if ($user->isGroupMember()) {
											$group = $user->getGroup();

											// If the user is a member of the technical group, put them in a privileged network.
											if ($group->getId() == 53) { // TODO: Make this more dynamic from event to event.
												$vlan = 24;
											} else {
												$vlan = 16;
											}
										}
										break;

									// Method: Wireless
									// NAS-Port-Type = Wireless-802.11
									case "Wireless-802.11":
										// If user is a group member, allow them to authenticate.
										if ($user->isGroupMember()) {
											$group = $user->getGroup();

											// If the user is a member of the technical group, put them in a privileged network.
											if ($group->getId() == 53) { // TODO: Make this more dynamic from event to event.
												$vlan = 24;
											} else {
												$vlan = 16;
											}
										// Allow paricipants to authenticate if user has a valid ticket and event is ongoing.
										} else if ($event->isOngoing() && $user->hasTicket()) {
											$vlan = 16;
										}
										break;
								}

								// If the VLAN parameter is set to a value higher than 1,
								if ($vlan > 1) {
									$reply = ['Tunnel-Type' => 'VLAN',
														'Tunnel-Medium-Type' => 'IEEE-802',
														'Tunnel-Private-Group-Id' => $vlan];
									$message = 'User succesfully post-authenticated.';
								} else {
									$message = 'User is lacking post-auth parameters.';
								}
							}
							break;
				 }
			 } else {
				 $message = Localization::getLocale('this_user_does_not_exist');
			 }
		 } else {
			$message = 'The username is invalid.';
		 }
		} else {
		 $message = 'The requested action is not supported.';
		}
	} else {
		$message = 'The API key did not match.';
	}
} else {
	$message = 'Invalid API key in use.';
}

$reply['reply:Reply-Message'] = $message;

function isAllowedToAuthenticate(User $user) {
	$event = EventHandler::getCurrentEvent();

	return $user->isGroupMember() || ($event->isOngoing() && $user->hasTicket());
}

header('Content-Type: application/json');
echo json_encode($reply, JSON_PRETTY_PRINT);
Database::cleanup();
?>
