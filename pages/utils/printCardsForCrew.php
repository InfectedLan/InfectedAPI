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

require_once 'handlers/userhandler.php';
require_once 'handlers/grouphandler.php';
require_once 'objects/user.php';
require_once 'utils/cardgenerator.php';

if(!isset($_GET["id"])) {
    die("User id not set");
}

$group = GroupHandler::getGroup($_GET['id']);
if($group == null) {
    die("Group does not exist");
}

$members = $group->getMembers();


$za = new ZipArchive();

$randomizedName = md5(time());

if ($za->open(Settings::getValue("api_path") . 'content/cards/' . $randomizedName . '.zip', ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <" . Settings::getValue("api_path") . 'content/cards/' . $randomizedName . '.zip' . ">\n");
}

$tmpFile = Settings::getValue("api_path") . 'content/cards/tmp/';

$counter = 0;
foreach($members as $member) {
    //$randomName = md5(time()) . '.png';
    $randomName = md5($member->getId() . '.' . $member->getGroup()->getId() . '.' . $member->getGroup()->getName() . '.' . $member->getDisplayName());
    if($member->isTeamMember()) {
        $randomName = md5($randomName . $member->getTeam()->getId() . '.' . $member->getTeam()->getName());
    }
    if(!file_exists($tmpFile . $randomName)) {
        $image = CardGenerator::generateCard($member);
        imagepng($image, $tmpFile . $randomName);
    }

    $za->addFile($tmpFile . $randomName, $counter++ . ':' . $member->getFirstName() . '.png');
}

$za->close();

header('Content-Type: application/zip');

header('Content-Disposition: attachment; filename="' . $group->getName() . '.zip"');

readfile(Settings::getValue("api_path") . 'content/cards/' . $randomizedName . '.zip');


?>
