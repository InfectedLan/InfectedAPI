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
require_once 'handlers/userhandler.php';
$result = false;
$message = "";
$data = null;

if(Session::isAuthenticated()) {
    $user = Session::getCurrentUser();
    if($_GET["id"] == null) {
        $target = $user;
    }
    $message = Localization::getLocale('this_user_does_not_exist');
    if(isset($_GET['id'])) {
        if($user->hasPermission('user.search')) {
            $target = UserHandler::getUser($_GET['id']);
        } else if(strtoint($_GET['id']) == $user->getId()) {
            $target = UserHandler::getUser($_GET['id']);
        } else {
            $message = Localization::getLocale('you_do_not_have_permission_to_do_that');
        }
    }
    if(isset($target)) {
        $data = array("id" => $target->getId(),
                      "firstname" => $target->getFirstname(),
                      "lastname" => $target->getLastname(),
                      "username" => $target->getUsername(),
                      "gender" => $target->getGender(),
                      "genderString" => $target->getGenderAsString(),
                      "nickname" => $target->getNickname(),
                      "displayName" => $target->getDisplayName(),
                      "age" => $target->getAge());
        //Get avatar info
        if($target->hasAvatar()) {
            $avatar = $target->getAvatar();
            $avatar_dat = array("hd" => $avatar->getHd(),
                                "sd" => $avatar->getSd(),
                                "thumb" => $avatar->getThumbnail());
            $data['avatar'] = $avatar_dat;
        }
        $result = true;
    } else {
        $result = false;
    }
} else {
    $message = Localization::getLocale('you_are_not_logged_in');
}
header('Content-Type: text/plain');
if($result) {
    echo json_encode(['result' => $result, 'data' => $data], JSON_PRETTY_PRINT);
} else {
    echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
}
?>