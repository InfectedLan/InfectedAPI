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
require_once 'secret.php';
require_once 'localization.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/serverhandler.php';
require_once 'handlers/compopluginhandler.php';

$result = false;
$message = null;
$data = null;

if (Session::isAuthenticated()) {
    $user = Session::getCurrentUser();

    if ($user->hasPermission('compo.management')) {
        if(isset($_GET['id']) && isset($_GET["url"])) {
	    $compo = CompoHandler::getCompo($_GET["id"]);
            if($compo != null) {
		$plugin = CompoPluginHandler::getPluginObjectOrDefault($compo->getPluginName());
		if(defined("Secret::toornamentApiKey") && defined("Secret::toornamentClientId") && defined("Secret::toornamentClientSecret")) {
		    //Stage 1: Authenticate with OAuth
		    
		    $oauthToken = $plugin->getToornamentOauthToken();
		    //echo "Toornament oauth token: " . $oauthToken;
		    
		    $compoId = str_replace("/", "", str_replace("https://organizer.toornament.com/tournaments/", "", $_GET["url"]));
		    $result = true;
		    $qualifiedClans = ClanHandler::getQualifiedClansByCompo($compo);

		    $curlUrl = "https://api.toornament.com/v1/tournaments/" . urlencode($compoId) . "/participants";
		    //echo "Sending requests to " . $curlUrl;
		    foreach($qualifiedClans as $clan) {
			$curlSess = curl_init();
			//Headers
			curl_setopt($curlSess, CURLOPT_URL, $curlUrl);
			curl_setopt($curlSess, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curlSess, CURLOPT_POSTFIELDS, json_encode($plugin->getToornamentParticipantData($clan)));
			curl_setopt($curlSess, CURLOPT_HTTPHEADER, array(
								   'Authorization: Bearer ' . $oauthToken,
								   'X-Api-Key: ' . Secret::toornamentApiKey
								   ));
			$curlResult = curl_exec($curlSess);
			$info = curl_getinfo($curlSess);
			if($info["http_code"] != 201) {
			    $data = json_decode($curlResult);
			    if(isset($data->errors)) {
				$message = "There was an error adding the clanid " . $clan->getId() . ": " . $curlResult;				
			    } else {
				$message = "There was an error adding the clanid " . $clan->getId() . ". We were not able to parse the error.";
			    }
			    $result = false;
			    break;
			}
			curl_close($curlSess);
		    }
		} else {
		    $message = "Toornament API key fields are missing from secret.php. Please add the fields \"toornamentApiKey\", \"toornamentClientId\", and \"toornamentClientSecret\" to secret.php.";
		}
            } else {
                $message = Localization::getLocale('this_compo_does_not_exist');
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

header('Content-Type: text/plain');
if($result) {
    echo json_encode(array('result' => $result), JSON_PRETTY_PRINT);
} else {
    echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}
?>
