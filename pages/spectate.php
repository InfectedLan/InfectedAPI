<?php
/**
 * This file is part of InfectedCrew.
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
require_once 'handlers/compohandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/compopluginhandler.php';
require_once 'objects/compo.php';

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('compo.casting')) {
	    $match = MatchHandler::getMatch($_GET["id"]);
	    if($match != null) {
		$compo = $match->getCompo();
		$javascripts = CompoPluginHandler::getPluginJavascriptOrDefault($compo->getPluginName());
		echo '<html>';
		echo '<head>';
		echo '<title>Infected CASTING page system </title>';
		echo '<script src="../scripts/jquery-1.11.3.min.js"></script>';
		echo '<script src="../scripts/websocket.js"></script>';
		echo '<script src="../scripts/match.js"></script>';
		echo '<script src="../scripts/spectate.js"></script>';
		echo '<script src="../scripts/compo.js"></script>';
		echo '<link rel="stylesheet" href="../styles/spectate.css">';
		echo '<link rel="stylesheet" type="text/css" href="../styles/bracket.css">';
		echo '</head>';
		echo '<body>';
		echo '<div class="fullscreen-bg"><video width="100%" height="100%" autoplay loop><source src="../content/static/casting_vid.mp4" type="video/mp4" /></video></div>';
		echo '<script>var api_path = "../"; var spectate_mode = true; getPageName = function() { return "currentMatch" };</script>';
		echo '<script>var compoListTask = new DownloadDatastoreTask("json/compo/getCompos.php", "compoList", function() {});var downloadManager = new PageDownloadWaiter([compoListTask], function() {Match.init(' . $match->getId() . ');});downloadManager.start();</script>';
		echo '<div id="allContent"><div id="mainContent"></div></div>';
		echo '</body>';
		echo '</html>';
	    } else {
		echo '<p>Matchen finnes ikke</p>';
	    }
	} else {
	    echo '<p>Du har ikke rettigheter til dette!</p>';
	}
} else {
	echo '<p>Du er ikke logget inn!</p>';
}
?>
