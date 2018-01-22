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
require_once 'handlers/bongtypehandler.php';
require_once 'handlers/bongentitlementhandler.php';
require_once 'handlers/nfccardhandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/bongtransactionhandler.php';
require_once 'localization.php';

$result = false;
$status = http_response_code();
$message = null;
$data = null;
$authenticated = false;

if(isset($_POST["pcbid"])) {
	$unit = NfcGateHandler::getGateByPcbid($_POST["pcbid"]);
	
	if($unit != null) {
		if(isset($_POST["cardid"])) {
			if(strlen($_POST["cardid"]) == 16) {
				$card = NfcCardHandler::getCardByNfcId($_POST["cardid"]);
				if($card != null) {
					$user = $card->getUser();
					if($user != null) {
						$bongTypes = BongTypeHandler::getBongTypes();
						$bongList = [];
						foreach ($bongTypes as $bong) {
							$entitlement = BongEntitlementHandler::calculateBongEntitlementByUser($bong, $user);
							if($entitlement != 0) {
								$posession = BongTransactionHandler::getBongPosession($bong, $user);
								$bongList[] = ["bong" => [ "id" => $bong->getId(),
													"name" => $bong->getName(),
											   		"description" => $bong->getDescription()],
											   		"posession" => $posession];
							}
						}

						$data = ["name" => $user->getDisplayName(),
								 "bongs" => $bongList];

						$status = 200;
						$result = true;
					} else {
						$status = 400;
						$message = Localization::getLocale('the_user_bound_to_the_card_does_not_exist');
					}
				} else {
					$status = 400;
					$message = Localization::getLocale('this_card_is_not_bound');
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
		$status = 403;
		$message = Localization::getLocale('invalid_pcbid');
	}
} else {
	$status = 400;
	$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
}


http_response_code($status);
header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message, 'data' => $data], JSON_PRETTY_PRINT);
Database::cleanup();