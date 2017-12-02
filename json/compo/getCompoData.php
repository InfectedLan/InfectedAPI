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

require_once 'session.php';
require_once 'database.php';
require_once 'localization.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/compopluginhandler.php';
require_once 'handlers/clanhandler.php';

$result = false;
$message = null;
$data = null;

if(Session::isAuthenticated()) {
    if(isset($_GET["id"])) {
        $compo = CompoHandler::getCompo($_GET["id"]);
        if($compo != null) {
            //Return some info on the compo, to be used when displaying the compo
            $data = [];
            //First up: Clan data
            $clanData = [];
            $clans = ClanHandler::getClansByCompo($compo);
            foreach($clans as $clan) {
                $clanData[] = ["name" => $clan->getName(),
                               "tag" => $clan->getTag(),
                               "id" => $clan->getId(),
			       "qualified" => $clan->isQualified($compo)];
            }
            $data["clans"] = $clanData;
	    $data["hasMatches"] = CompoHandler::hasGeneratedMatches($compo);
            $result = true;
        } else {
            $message = Localization::getLocale("this_compo_does_not_exist");
        }
    } else {
        $message = Localization::getLocale("you_have_not_filled_out_the_required_fields");
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
Database::cleanup();
?>
