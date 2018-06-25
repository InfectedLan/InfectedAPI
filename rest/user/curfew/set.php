<?php
include 'database.php';
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

require_once 'session.php';
require_once 'localization.php';
require_once 'handlers/userhandler.php';

$result = false;
$status = http_response_code();
$message = null;

if (Session::isAuthenticated()) {
    $user = Session::getCurrentUser();

    if ($user->hasPermission('nfc.curfew')) {
        if (isset($_POST['id']) &&
            is_numeric($_POST['id'])) {
            $editUser = UserHandler::getUser($_POST['id']);
            $swimming = isset($_POST['curfew']) ? $_POST['curfew'] : 0;

            if ($editUser != null) {
                $editUser->setCurfew($_POST['curfew'] == '0'); //inverse
                $result = true;
                $status = 200;
            } else {
                $message = Localization::getLocale('the_user_does_not_exist');
            }
        } else {
            $message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
        }
    } else {
        $message = Localization::getLocale('you_do_not_have_permission_to_do_that');
    }
} else {
    $message = Localization::getLocale('you_are_not_logged_in');
}

http_response_code($status);
header('Content-Type: text/plain');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();
?>