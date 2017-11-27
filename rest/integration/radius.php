<?php
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

// Checking for valid API key.
if (isset($_GET['key']) &&
	!empty($_GET['key']) &&
	Secret::api_key == $_GET['key']) {

	if (isset($_GET['action']) &&
		isset($_GET['identifier']) &&
		!empty($_GET['action']) &&
		!empty($_GET['identifier'])) {
		$action = $_GET['action'];
		$identifier = $_GET['identifier'];

		if (UserHandler::hasUser($identifier)) {
			$user = UserHandler::getUserByIdentifier($identifier);
			$event = EventHandler::getCurrentEvent();

			switch ($action) {
				case "authorize":
					if (isset($_GET['password']) &&
						!empty($_GET['password'])) {

						if ($user->isActivated()) {
							$hashedPassword = hash('sha256', $_GET['password']);

							if (hash_equals($hashedPassword, $user->getPassword())) {
								if (isAllowedToAuthenticate($user)) {
									$reply = ['control:SHA2-Password' => $hashedPassword];
									$message = 'User \'' . $identifier .  '\' succesfully authorized.';

									SyslogHandler::log('User succesfully authorized.', 'radius', $user);
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
						isset($_GET['ip-address']) &&
						isset($_GET['device']) &&
						isset($_GET['mac-address']) &&
						!empty($_GET['port-type']) &&
						preg_match('/^((\.|^)(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]?|0$)){4}$/', $_GET['ip-address']) &&
						!empty($_GET['device']) &&
						preg_match('/^[0-9a-fA-F]{1,2}([\.:-])[0-9a-fA-F]{1,2}(?:\1[0-9a-fA-F]{1,2}){4}$/', $_GET['mac-address'])) {
						$portType = $_GET['port-type'];
						$ipAddress = $_GET['ip-address'];
						$device = $_GET['device'];
						$macAddress = $_GET['mac-address'];

						// Default VLAN for authorized users.
						$vlan = 16;

						switch ($portType) {
							// Method: Ethernet ("NAS-Port-Type = Ethernet")
							case "Ethernet":
							// Method: Wireless ("NAS-Port-Type = Wireless-802.11")
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
								}
								break;
						}

						$reply = ['Tunnel-Type' => 'VLAN',
											'Tunnel-Medium-Type' => 'IEEE-802',
											'Tunnel-Private-Group-Id' => $vlan];
						$message = 'User \'' . $identifier .  '\' succesfully post-authenticated, and was placed on network \'VLAN' . $vlan . '\'.';

						SyslogHandler::log('User succesfully post-authenticated', 'radius', $user, SyslogHandler::SEVERITY_INFO, ['Port-Type' => $portType,
																																																											'IP-Address' => $ipAddress,
																																																											'Device' => $device,
																																																											'MAC-Address' => $macAddress,
																																																											'VLAN' => $vlan]);
					} else {
						$message = 'Invalid input data for post-authentication.';
					}
		 	}
		} else {
			$message = Localization::getLocale('this_user_does_not_exist');
		}
	} else {
		$message = 'Invalid input data.';
	}
} else {
	$message = 'Invalid API key.';
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
