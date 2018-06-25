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
require_once 'handlers/passwordresetcodehandler.php';

$result = false;
$status = http_response_code();
$message = null;

if (isset($_POST['code']) &&
	isset($_POST['password']) &&
    isset($_POST['confirm-password']) &&
    !empty($_POST['code']) &&
    !empty($_POST['password']) &&
    !empty($_POST['confirm-password'])) {
    $code = $_POST['code'];
    $password = hash('sha256', $_POST['password']);
    $confirmPassword = hash('sha256', $_POST['confirm-password']);

    if (PasswordResetCodeHandler::hasPasswordResetCode($code)) {
        $user = PasswordResetCodeHandler::getUserFromPasswordResetCode($code);

        if (hash_equals($password, $confirmPassword)) {
            PasswordResetCodeHandler::removePasswordResetCode($code);
            UserHandler::updateUserPassword($user, $password);
            $result = true;
            $status = 202; // Accepted.
            $message = Localization::getLocale('your_password_is_now_changed');
        } else {
            $message = Localization::getLocale('passwords_does_not_match');
        }
    } else {
        $status = 404; // Not found.
        $message = Localization::getLocale('the_link_to_reset_your_password_is_no_longer_valid');
    }
} else {
    $status = 400; // Bad Request.
    $message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
}

http_response_code($status);
header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();
