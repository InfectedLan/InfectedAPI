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
require_once 'localization.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/compopluginhandler.php';

$result = false;
$message = "";
$data = null;

if(Session::isAuthenticated()) {
    $compos = CompoHandler::getComposByEvent(EventHandler::getCurrentEvent());
    $data = array();
    foreach($compos as $compo) {
        $data[] = ["id" => $compo->getId(),
                   "name" => $compo->getName(),
                   "title" => $compo->getTitle(),
                   "tag" => $compo->getTag(),
		   "chat" => $compo->getChatId(),
                   "description" => $compo->getDescription(),
		   "teamSize" => $compo->getTeamSize(),
		   "participantLimit" => $compo->getParticipantLimit(),
		   "hasMatches" => CompoHandler::hasGeneratedMatches($compo),
		   "requiresSteam" => $compo->requiresSteamId(),
		   "pluginName" => $compo->getPluginName(),
                   "pluginJavascript" => CompoPluginHandler::getPluginJavascriptOrDefault($compo->getPluginName())];
    }
    $result = true;
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