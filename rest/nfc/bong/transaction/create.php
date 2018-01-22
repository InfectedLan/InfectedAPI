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
require_once 'handlers/sysloghandler.php';

$result = false;
$status = http_response_code();
$message = null;
$authenticated = false;

if(isset($_POST["pcbid"])) {
	if(strlen($_POST["pcbid"]) == 32) {
		$unit = NfcGateHandler::getGateByPcbid($_POST["pcbid"]);

		if($unit != null) {
			if(isset($_POST["cardid"]) && 
				isset($_POST["transactorId"]) && 
				isset($_POST["bongType"]), && 
				isset($_POST["amount"]) && 
				is_numeric($_POST["bongType"]) && 
				is_numeric($_POST["amount"])) {

				if(strlen($_POST["cardid"]) == 16 && strlen($_POST["transactorId"]) == 16) {
					$card = NfcCardHandler::getCardByNfcId($_POST["cardid"]);
					$transactor = NfcCardHandler::getCardByNfcId($_POST["transactorId"]);

					if($card != null) {
						$type = BongTypeHandler::getBongType($_POST["bongType"]);
						if($type != null) {
							if($transactor != null) {
								$user = $card->getUser();
								$transactorUser = $transactor->getUser();

								if($user != null) {
									if($transactorUser != null) {
										$funds = BongTransactionHandler::getBongPosession($bongType, $user);
										if($funds+$_POST["amount"]>=0) {
											BongTransactionHandler::processBongTransaction($bongType, $user, $_POST["amount"], $transactorUser);
											
											$status = 200;
											$result = true;
										} else {
											$status = 400;
											$message = Localization::getLocale('not_enough_bongs');
										}
									} else {
										$status = 400; // Bad Request.
										$message = Localization::getLocale('the_transactor_user_does_not_exist');
									}
								} else {
									$status = 400; // Bad Request.
									$message = Localization::getLocale('the_user_bound_to_the_card_does_not_exist');
								}
							} else {
								$status = 400;
								$message = Localization::getLocale('the_transactor_card_is_not_bound');
							}
						} else {
							$status = 400;
							$message = Localization::getLocale('the_bong_type_doesnt_exist');
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
			SyslogHandler::log("NFC bong transaction was attempted with invalid pcbid", "nfc/bong/transaction/create", null, SyslogHandler::WARNING, ["pcbid" => $_POST["pcbid"], "ip" => $_SERVER['REMOTE_ADDR']]);
		}
	} else {
		$status = 403;
		$message = Localization::getLocale('invalid_pcbid');
		SyslogHandler::log("NFC bong transaction was attempted with malformed pcbid", "nfc/bong/transaction/create", null, SyslogHandler::WARNING, ["pcbid" => $_POST["pcbid"], "ip" => $_SERVER['REMOTE_ADDR']]);
	}
} else {
	$status = 400;
	$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
}


http_response_code($status);
header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();