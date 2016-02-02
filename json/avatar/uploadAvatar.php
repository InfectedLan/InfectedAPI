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

require_once 'session.php';
require_once 'localization.php';
require_once 'handlers/avatarhandler.php';
require_once 'handlers/sysloghandler.php';

$result = false;
$message = Localization::getLocale('an_unknown_error_occurred');

try {
    if (Session::isAuthenticated()) {
        $user = Session::getCurrentUser();

        // Remove avatar if the user already have one.
        if ($user->hasAvatar()) {
            $user->getAvatar()->remove();
        }

        $temp = explode('.', $_FILES['file']['name']);
        $extension = strtolower(end($temp));
        $allowedExts = ['jpeg', 'jpg', 'png'];

        if (($_FILES['file']['size'] < 15 * 1024 * 1024)) {
            if (in_array($extension, $allowedExts)) {
                if ($_FILES['file']['error'] == 0) {
                    // Validate size
                    $image = 0;

                    if ($extension == 'png') {
                        $image = imagecreatefrompng($_FILES['file']['tmp_name']);
                    } else if ($extension == 'jpeg' ||
                               $extension == 'jpg') {
                        $image = imagecreatefromjpeg($_FILES['file']['tmp_name']);
                    }

                    if (imagesx($image) >= Settings::avatar_minimum_width && imagesy($image) >= Settings::avatar_minimum_height) {
                        $name = bin2hex(openssl_random_pseudo_bytes(16)) . $user->getUsername();
                        $path = AvatarHandler::createAvatar($name . '.' . $extension, $user);
                        move_uploaded_file($_FILES['file']['tmp_name'], $path);
                        $result = true;
			SyslogHandler::log("Avatar uploaded", "uploadAvatar", $user, SyslogHandler::SEVERITY_INFO, array("filename" => $name, "width" => imagesx($image), "height" => imagesy($image), "extension" => $extension));
                    } else {
                        $message = Localization::getLocale('the_image_is_too_small_it_must_be_at_least_value_pixels', Settings::avatar_minimum_width . ' x ' . Settings::avatar_minimum_height);
                    }
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
        $message = Localization::getLocale('you_are_not_logged_in');
    }
} catch(Exception $e) {
    $message = Localization::getLocale('an_exception_occurred', $e);
}
header('Content-Type: text/plain');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
?>
