<?php
include 'database.php';
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

require_once 'session.php';
require_once 'settings.php';
require_once 'localization.php';
require_once 'handlers/avatarhandler.php';
require_once 'handlers/sysloghandler.php';

$result = false;
$uploadedName = null;
$message = Localization::getLocale('an_unknown_error_occurred');

try {
    if (Session::isAuthenticated()) {
        $user = Session::getCurrentUser();
        if($user->hasPermission("compo.casting")) {
            $temp = explode('.', $_FILES['file']['name']);
            $extension = strtolower(end($temp));
            $allowedExts = ['jpeg', 'jpg', 'png'];

            if (($_FILES['file']['size'] < 15 * 1024 * 1024)) {
                if (in_array($extension, $allowedExts)) {
                    if ($_FILES['file']['error'] == 0) {
                        move_uploaded_file($_FILES['file']['tmp_name'], Settings::api_path . "content/castingAssets/" .  $_FILES['file']['name']);
                        $uploadedName = $_FILES['file']['name'];
                        $result = true;
                    } else {
                        $message = Localization::getLocale('an_internal_error_occurred_when_uploading_image', $_FILES['file']['error']);
                    }
                } else {
                    $message = Localization::getLocale('invalid_file_format');
                }
            } else {
                $message = Localization::getLocale('the_file_size_is_too_large');
            }
        } else {
            $message = Localization::getLocale('you_do_not_have_permission_to_do_that');
        }
    } else {
        $message = Localization::getLocale('you_are_not_logged_in');
    }
} catch(Exception $e) {
    $message = Localization::getLocale('an_exception_occurred', $e);
}
header('Content-Type: text/plain');
if($result) {
    echo json_encode(['result' => $result, 'uploadedName' => $uploadedName], JSON_PRETTY_PRINT);
} else {
    echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}

?>
