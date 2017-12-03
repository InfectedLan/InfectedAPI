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
require_once 'handlers/networkhandler.php';
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
			
			switch ($action) {
				case 'authorize':
					if (isset($_GET['password']) &&
						!empty($_GET['password'])) {

						if ($user->isActivated()) {
							$hashedPassword = hash('sha256', $_GET['password']);

							if (hash_equals($hashedPassword, $user->getPassword())) {
								if (isAllowedToAuthenticate($user)) {
									$reply = ['control:SHA2-Password' => $hashedPassword];
									$message = 'User \'' . $user->getUsername() .  '\' succesfully authorized.';

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

		    case 'post-auth':
					if (isset($_GET['port-type']) &&
						isset($_GET['ip-address']) &&
						isset($_GET['device']) &&
						isset($_GET['mac-address']) &&
						!empty($_GET['port-type']) &&
						preg_match('/^((\.|^)(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]?|0$)){4}$/', $_GET['ip-address']) &&
						!empty($_GET['device']) &&
						preg_match('/^[0-9a-fA-F]{1,2}([\.:-])[0-9a-fA-F]{1,2}(?:\1[0-9a-fA-F]{1,2}){4}$/', $_GET['mac-address'])) {
						$networkType = NetworkHandler::getNetworkTypeByPortType($_GET['port-type']);
						$ipAddress = $_GET['ip-address'];
						$device = $_GET['device'];
						$macAddress = $_GET['mac-address'];

						if ($networkType != null) {
							// Method: Ethernet ("NAS-Port-Type = Ethernet")
							// Method: Wireless ("NAS-Port-Type = Wireless-802.11")

							// Does the user have network access on the given port type.
							if ($user->hasNetworkAccess($networkType)) {
								$network = $user->getNetwork($networkType);

								// Add vlan specific parameters to the reply.
								$reply = ['Tunnel-Type' => 'VLAN',
													'Tunnel-Medium-Type' => 'IEEE-802',
													'Tunnel-Private-Group-Id' => $network->getVlanId()];
								$message = 'User \'' . $user->getUsername() .  '\' succesfully post-authenticated, with port-type \'' . $networkType->getTitle() . '\' and vlan id \'' . $network->getVlanId() . '\'.';

								// We log this to syslog.
								SyslogHandler::log('User succesfully post-authenticated', 'radius', $user, SyslogHandler::SEVERITY_INFO, ['Port-Type' => $networkType->getPortType(),
																																																													'IP-Address' => $ipAddress,
																																																													'Device' => $device,
																																																													'MAC-Address' => $macAddress,
																																																													'VLAN' => $network->getVlanId()]);
							} else {
								$message = 'User don\'t have access to this network.';
							}
						} else {
							$message = 'Invalid port-type for post-authentication.';
						}
					} else {
						$message = 'Invalid input data for post-authentication.';
					}
					break;

				case 'accounting':
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

						// TODO: Implement backend for this.
					} else {
						$message = 'Invalid input data for accounting.';
					}
					break;
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

	return $user->isGroupMember() || ($event->isOngoing() && $user->hasTicket()); // TODO: Move this to handler.
}

header('Content-Type: application/json');
echo json_encode($reply, JSON_PRETTY_PRINT);
Database::cleanup();
?>
