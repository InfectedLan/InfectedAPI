<?php

require_once 'session.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/compopluginhandler.php';

if(Session::isAuthenticated()) {
    $user = Session::getCurrentUser();
    if ($user->hasPermission('compo.management')) {
	if(isset($_GET["id"]) &&
	   !empty($_GET["id"])) {
	    $compo = CompoHandler::getCompo($_GET["id"]);
	    if($compo != null) {
		echo "<h1>Transfer to toornament</h1>";
		echo "<p>This feature does a dumb transfer of all clans and participants to toornament. Note that changes done there will not affect infected.no</p>";
		$plugin = CompoPluginHandler::getPluginObjectOrDefault($compo->getPluginName());
		echo "<h3>Send data</h3>";
		echo '<div id="toornamentSendArea"><form id="toornamentForm"><input type="text" placeholder="Toornament ID" name="toornament_id" /><input type="hidden" name="id" value="' . $compo->getId() . '" /></form><input type="button" value="Send data" id="toornamentSendBtn"/></div>';
		echo '<div id="toornamentLoadingArea" style="display: none;"><i>Sending data....</i></div>';
		echo "<h3>Review</h3>";
		echo "<p>The following information will be transferred to toornament. Please skim for obvious errors</p>";
		$qualifiedClans = ClanHandler::getQualifiedClansByCompo($compo);
		foreach($qualifiedClans as $clan) {
		    echo "<pre>";

		    echo htmlspecialchars(json_encode($plugin->getToornamentParticipantData($clan), JSON_PRETTY_PRINT));
		    
		    echo "</pre>";
		}
		echo '<script src="../api/scripts/toornamentTransfer.js"></script>';
	    } else {
		echo "<h1>The compo does not exist</h1>";
	    }
	} else {
	    echo "<h1>Missing arguments</h1>";
	}
    } else {
	echo "<h1>Du har ikke tillatelse til å være her</h1>";
    }
} else {
    echo "<h1>Du er ikke logget inn!</h1>";
}
?>