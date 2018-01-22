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

require_once 'session.php';
require_once 'database.php';
require_once 'handlers/nfcgatehandler.php';
require_once 'handlers/nfccardhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/tickethandler.php';
require_once 'localization.php';
require_once 'handlers/sysloghandler.php';

$result = false;
$status = http_response_code();
$message = null;
$authenticated = false;

if(isset($_POST["pcbId"])) {
	if(strlen($_POST["pcbId"]) == 32) {
		$unit = NfcGateHandler::getGateByPcbid($_POST["pcbId"]);
		if($unit != null) {
			if($unit->getType()==NfcGate::NFC_GATE_TYPE_TICKETSCANNER) {
				if(isset($_POST["cardId"]) && isset($_POST["userId"])) {
					if(strlen($_POST["cardId"])==16) {
						$card = NfcCardHandler::getCardByNfcId($_POST["cardId"]);
						if($card==null) {
							$user = UserHandler::getUser($_POST["userId"]);
							if($user!=null) {
								NfcCardHandler::registerCard($user, $_POST["cardId"]);
								$status = 200;
								$result = true;
							} else {
								$status = 400; // Bad Request.
								$message = Localization::getLocale('this_user_does_not_exist');
							}
						} else {
							$status = 400;
							$message = Localization::getLocale('the_card_is_already_bound');
						}
					} else {
						$status = 400; // Bad Request.
						$message = Localization::getLocale('invalid_cardid_format');	
					}
				} else {
					$status = 400; // Bad Request.
					$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
				}
			} else {
				$status = 403; // Forbidden
				$message = Localization::getLocale('invalid_pcbid');
				SyslogHandler::log("NFC user registration was attempted with an unit which is not a ticketscanner", "nfc/user/create", null, SyslogHandler::SEVERITY_WARNING, ["type" => $unit->getType(), "pcbId" => $_GET["pcbId"], "ip" => $_SERVER['REMOTE_ADDR']]);
			}
		} else {
			$status = 403;
			$message = Localization::getLocale('invalid_pcbid');
			SyslogHandler::log("NFC user registration was attempted with invalid pcbid", "nfc/user/create", null, SyslogHandler::SEVERITY_WARNING, ["pcbId" => $_POST["pcbId"], "ip" => $_SERVER['REMOTE_ADDR']]);
		}
	} else {
		$status = 403;
		$message = Localization::getLocale('invalid_pcbid');
		SyslogHandler::log("NFC user registration was attempted with malformed pcbid", "nfc/user/create", null, SyslogHandler::SEVERITY_WARNING, ["pcbId" => $_POST["pcbId"], "ip" => $_SERVER['REMOTE_ADDR']]);
	}
} else {
	$status = 400;
	$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
}


http_response_code($status);
header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();