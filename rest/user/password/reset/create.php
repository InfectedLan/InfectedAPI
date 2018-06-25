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
require_once 'localization.php';
require_once 'handlers/userhandler.php';

$result = false;
$status = http_response_code();
$message = null;

if (isset($_POST['identifier']) &&
    !empty($_POST['identifier'])) {
    $identifier = $_POST['identifier'];

    if (UserHandler::hasUser($identifier)) {
        $user = UserHandler::getUserByIdentifier($identifier);

        if ($user != null) {
            $user->sendPasswordResetEmail();
            $result = true;
            $status = 201; // Created.
            $message = Localization::getLocale('an_email_has_been_sent_to_your_registered_address_click_the_link_to_change_your_password');
        }
    } else {
        $status = 404; // Not found.
        $message = Localization::getLocale('could_not_find_the_user_in_the_database');
    }
} else {
    $status = 400; // Bad Request.
    $message = Localization::getLocale('you_must_enter_a_username_an_email_address_or_a_phone_number');
}

http_response_code($status);
header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();