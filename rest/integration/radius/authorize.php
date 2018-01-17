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

            if (isset($_GET['password']) &&
                !empty($_GET['password'])) {
                if ($user->isActivated()) {
                    $hashedPassword = hash('sha256', $_GET['password']);

                    if (hash_equals($hashedPassword, $user->getPassword())) {
                        if (isAllowedToAuthenticate($user)) {
                            $reply = ['control:SHA2-Password' => $hashedPassword];
                            $message = 'User \'' . $user->getUsername() .  '\' succesfully authorized.';

                            SyslogHandler::log('User succesfully authorized.', 'rest/integration/radius/authorize', $user);
                        } else {
                            $message = 'You\'re not allowed to use this service.';
                        }
                    } else {
                        $message = Localization::getLocale('wrong_username_or_password');
                    }
                } else {
                    $message = Localization::getLocale('you_must_activate_your_user_account_in_order_to_logg_in');
                }
            } else {
                $status = 400; // Bad Request.
                $message = 'Invalid input data for authorization.';
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

function isAllowedToAuthenticate(User $user) {
    $event = EventHandler::getCurrentEvent();

    return $user->isGroupMember() || ($event->isOngoing() && $user->hasTicket()); // TODO: Move this to handler.
}

if ($message != null) {
	$reply['reply:Reply-Message'] = $message;
}

http_response_code($status);
header('Content-Type: application/json');
echo json_encode($reply, JSON_PRETTY_PRINT);
Database::cleanup();