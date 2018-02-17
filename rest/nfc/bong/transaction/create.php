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
require_once 'handlers/nfcunithandler.php';
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

$targetUser = null;
$transactorUser = null;

if (Session::isAuthenticated()) {
    $user = Session::getCurrentUser();

    if ($user->hasPermission('nfc.bong.transaction')) {
        if( isset($_POST['userId'])) {
            $targetUser = UserHandler::getUser($_POST['userId']);
            if($targetUser != null) {
                $transactorUser = $user;
                if($transactorUser != null) {
                    $authenticated = true;
                } else {
                    $status = 400; // Bad Request.
                    $message = Localization::getLocale('the_transactor_user_does_not_exist');
                }
            } else {
                $status = 400; // Bad Request.
                $message = Localization::getLocale('the_target_user_does_not_exist');
            }
        } else {
            $status = 400; // Bad Request.
            $message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
        }
    } else {
        $status = 403; // Forbidden
        $message = Localization::getLocale('you_do_not_have_permission_to_do_that');
    }
} elseif(isset($_POST["pcbId"])) {
    if (strlen($_POST["pcbId"]) == 32) {
        $unit = NfcGateHandler::getGateByPcbid($_POST["pcbId"]);

        if ($unit != null) {
            if ($unit->getType() == NfcUnit::NFC_GATE_TYPE_POS) {
                if(isset($_POST["cardId"]) &&
                    isset($_POST["transactorCardId"])) {
                    if(strlen($_POST["cardId"]) == 16 && strlen($_POST["transactorId"]) == 16) {
                        $card = NfcCardHandler::getCardByNfcId($_POST["cardId"]);
                        $transactor = NfcCardHandler::getCardByNfcId($_POST["transactorId"]);

                        if($card != null) {
                            if($transactor != null) {
                                $targetUser = $card->getUser();
                                $transactorUser = $transactor->getUser();
                                if($targetUser != null) {
                                    if($transactorUser != null) {
                                        $authenticated = true;
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
                SyslogHandler::log("Bong posession fetching was attempted with an unit which is not a POS device", "nfc/bong/transaction/create", null, SyslogHandler::SEVERITY_WARNING, ["type" => $unit->getType(), "pcbId" => $_POST["pcbId"], "ip" => $_SERVER['REMOTE_ADDR']]);
            }
        } else {
            $status = 403;
            $message = Localization::getLocale('invalid_pcbid');
            SyslogHandler::log("NFC bong transaction was attempted with invalid pcbid", "nfc/bong/transaction/create", null, SyslogHandler::SEVERITY_WARNING, ["pcbId" => $_POST["pcbId"], "ip" => $_SERVER['REMOTE_ADDR']]);
        }
    } else {
        $status = 403;
        $message = Localization::getLocale('invalid_pcbid');
        SyslogHandler::log("NFC bong transaction was attempted with malformed pcbid", "nfc/bong/transaction/create", null, SyslogHandler::SEVERITY_WARNING, ["pcbId" => $_POST["pcbId"], "ip" => $_SERVER['REMOTE_ADDR']]);
    }
} else {
    $status = 400;
    $message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
}

if($authenticated) {
    if( isset($_POST["bongType"]) &&
        isset($_POST["amount"]) &&
        is_numeric($_POST["bongType"]) &&
        is_numeric($_POST["amount"])) {

        $type = BongTypeHandler::getBongType($_POST["bongType"]);
        if($type != null) {
            $funds = BongTransactionHandler::getBongPosession($type, $targetUser);
            if($funds+$_POST["amount"]>=0) {
                BongTransactionHandler::processBongTransaction($type, $targetUser, $_POST["amount"], $transactorUser);
                $status = 200;
                $result = true;
            } else {
                $status = 400;
                $message = Localization::getLocale('not_enough_bongs');
            }
        } else {
            $status = 400;
            $message = Localization::getLocale('the_bong_type_doesnt_exist');
        }

    } else {
        $status = 400; // Bad Request.
        $message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
    }
}
http_response_code($status);
header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();