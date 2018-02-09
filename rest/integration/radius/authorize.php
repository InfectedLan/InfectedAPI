<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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
$status = http_response_code();
$message = null;

// Checking for valid API key.
if (!empty($_GET['key']) &&
	Secret::api_key == $_GET['key']) {

    if (isset($_GET['identifier']) &&
        !empty($_GET['identifier'])) {
		$identifier = $_GET['identifier'];

		if (UserHandler::hasUser($identifier)) {
			$user = UserHandler::getUserByIdentifier($identifier);

            if ($user->isActivated()) {
                if (isset($_GET['port-type']) &&
                    isset($_GET['device-ip-address']) &&
                    isset($_GET['device-mac-address-ssid']) &&
                    isset($_GET['client-mac-address']) &&
                    !empty($_GET['port-type']) &&
                    preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $_GET['device-ip-address']) &&
                    !empty($_GET['device-mac-address-ssid']) &&
                    preg_match('/^[0-9a-fA-F]{1,2}([\.:-])[0-9a-fA-F]{1,2}(?:\1[0-9a-fA-F]{1,2}){4}$/', $_GET['client-mac-address'])) {
                    $networkType = NetworkHandler::getNetworkTypeByPortType($_GET['port-type']);
                    $deviceIpAddress = $_GET['device-ip-address'];
                    $deviceMacAddressSsid = $_GET['device-mac-address-ssid'];
                    $clientMacAddress = $_GET['client-mac-address'];

                    if ($networkType != null) {
                        // Does the user have network access on the given port type.
                        if (isAllowedToAuthorize($user) && $user->hasNetworkAccess($networkType)) {
                            $reply = ['control:SHA2-Password' => $user->getPassword()];
                            $message = 'User \'' . $user->getUsername() .  '\' succesfully authorized.';

                            SyslogHandler::log('User succesfully authorized.', 'rest/integration/radius/authorize', $user);
                        } else {
                            $message = 'User does not have access to this service.';
                        }
                    } else {
                        $status = 400; // Bad Request.
                        $message = 'Invalid port-type for authorization.';
                    }
                } else {
                    $status = 400; // Bad Request.
                    $message = 'Invalid input data for authorization.';
                }
            } else {
                $message = Localization::getLocale('you_must_activate_your_user_account_in_order_to_log_in');
            }
		} else {
            $status = 404; // Not found.
			$message = Localization::getLocale('this_user_does_not_exist');
		}
	} else {
        $status = 400; // Bad Request.
		$message = 'Invalid input data.';
	}
} else {
    $status = 401; // Unauthorized.
	$message = 'Invalid API key.';
}

if ($message != null) {
	$reply['reply:Reply-Message'] = $message;
}

function isAllowedToAuthorize(User $user): bool {
    $event = EventHandler::getCurrentEvent();

    return $user->isGroupMember() || ($event->isOngoing() && $user->hasTicket());
}

http_response_code($status);
header('Content-Type: application/json');
echo json_encode($reply, JSON_PRETTY_PRINT);
Database::cleanup();