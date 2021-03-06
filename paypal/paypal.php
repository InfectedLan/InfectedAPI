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
require_once 'paypal/callerservice.php';
require_once 'paypalsecret.php';
require_once 'handlers/sysloghandler.php';

class PayPal {
	public static function getPaymentUrl($ticketType, $amount, $key, $user) {
		$currencyCodeType = "NOK";
		$paymentType = "Sale";

		//Set the return and cancel url
		/*$returnURL =urlencode('https://tickets.infected.no/v2/index.php?page=reviewOrder');
		$cancelURL =urlencode('https://tickets.infected.no/v2/index.php');*/

		//$returnURL = urlencode('https://tickets.infected.no/v2/index.php?page=reviewOrder');
		//$cancelURL = urlencode('https://tickets.infected.no/v2/index.php');

		//Calculate total price
		$itemamt = $ticketType->getPriceByUser($user, $amount);
		$amt = $itemamt;
		$maxamt= $amt;

		$nvpstr = "&_LITEMCATEGORY0=Digital&NOSHIPPING=1&L_NAME0=" . $ticketType->getTitle() . "&L_AMT0=" . $itemamt .
		"&L_QTY0=" . 1 . "&MAXAMT=" . (string)$itemamt . "&AMT=" . (string)$itemamt . "&ITEMAMT=" .
		(string)$itemamt . "&CALLBACKTIMEOUT=4&L_NUMBER0=10001&L_DESC0=" . $ticketType->getTitle() .
		"&ReturnUrl=" . PaypalSecret::ReturnUrl . "&CANCELURL=" . PaypalSecret::CancelUrl ."&CURRENCYCODE=" . $currencyCodeType .
		"&PAYMENTACTION=" . $paymentType;

		$nvpstr = /*$nvpHeader .*/$nvpstr;

		//Make the call to PayPal to get the Express Checkout token
		$resArray=hash_call("SetExpressCheckout",$nvpstr);
		$_SESSION['reshash']=$resArray;

		$ack = strtoupper($resArray["ACK"]);

		if($ack=="SUCCESS"){
			$token = urldecode($resArray["TOKEN"]);
			$url = PaypalSecret::PaypalUrl . $token;
			SyslogHandler::log("Got payment url from paypal", "paypal", $user, SyslogHandler::SEVERITY_INFO, array("ticketType" => ($ticketType != null ? $ticketType->getId() : "null"), "amount" => $amount, "session" => $key, "paypalResponse" => $resArray));
			return $url;
		} else  {
		    SyslogHandler::log("Error fetching paypal token", "paypal", $user, SyslogHandler::SEVERITY_WARNING, $resArray);
            /*
            //Report the error somewhere for debugging
            $errorLog = "===BEGIN ERROR LOG===\n" . $nvpstr . "\n===BEGIN_RESPONSE===\n" . print_r($resArray, true) . "\n";
            $file = "/home/infected.no/logs/paypalerror";
            $current = file_get_contents($file);
            $current .= $errorLog;
            file_put_contents($file, $current);
            */
			return null;
		}
	}

	public static function completePurchase($token, $paymentAmount, $currCodeType, $payerID, $serverName) {
	    $user = Session::getCurrentUser();
		ini_set('session.bug_compat_42',0);
		ini_set('session.bug_compat_warn',0);

		$nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION=Sale&AMT='.
		$paymentAmount.'&CURRENCYCODE='.$currCodeType.'&IPADDRESS='.$serverName ;

		 /* Make the call to PayPal to finalize payment
			If an error occured, show the resulting errors
			*/
		$resArray=hash_call("DoExpressCheckoutPayment",$nvpstr);

		/* Display the API response back to the browser.
		   If the response from PayPal was a success, display the response parameters'
		   If the response was an error, display the errors received using APIError.php.
		   */
		$ack = strtoupper($resArray["ACK"]);
		if($ack == "SUCCESS") {

			$paymentstatus = strtoupper($resArray["PAYMENTSTATUS"]);

			if($paymentstatus == "COMPLETED") {
				$transid = strtoupper($resArray["TRANSACTIONID"]);
				SyslogHandler::log("Payment completion success", "paypal", $user, SyslogHandler::SEVERITY_INFO, array("price" => $paymentAmount, "token" => $token, "paypalResponse" => $resArray));
				return $transid;
			} else {
			    SyslogHandler::log("Payment status not completed! Error?", "paypal", $user, SyslogHandler::SEVERITY_WARNING, array("price" => $paymentAmount, "token" => $token, "paypalResponse" => $resArray));
			}
		} else {
		    SyslogHandler::log("Payment completion failed!", "paypal", $user, SyslogHandler::SEVERITY_WARNING, array("price" => $paymentAmount, "token" => $token, "paypalResponse" => $resArray));
		}
		return null;
		/*
		if($ack != 'SUCCESS' && $ack != 'SUCCESSWITHWARNING' && $paymentstatus != 'COMPLETED'){
			return null;
		}
		if($ack == 'SUCCESS' && $paymentstatus == "COMPLETED"){
			$transid = strtoupper($resArray["TRANSACTIONID"]);
			return $transid;
		}
		return null;*/
	}
	public static function getExpressCheckoutDetails($token) {

		$nvpstr="&TOKEN=".urlencode($token);

		//echo $nvpstr;

		$resArray = hash_call("GetExpressCheckoutDetails",$nvpstr);

		$ack = strtoupper($resArray["ACK"]);

		if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING'){
			return $resArray;
		} else {
			return null;
		}
	}
	//Not working
	public static function handlePaypalRedirectData() {
		//This is stuff done in the php called by paypal.
		$_SESSION['token']=$_REQUEST['token'];
		$_SESSION['payer_id'] = $_REQUEST['PayerID'];

		$_SESSION['paymentAmount']=$_REQUEST['paymentAmount'];
		$_SESSION['currCodeType']=$_REQUEST['currencyCodeType'];
		$_SESSION['paymentType']="Authorization";

		$resArray=$_SESSION['reshash'];
		$_SESSION['TotalAmount']= $resArray['AMT'] + $resArray['SHIPDISCAMT'];

		$_SESSION["name"] = $resArray["FIRSTNAME"]. $resArray["LASTNAME"];
		$_SESSION["email"] = $resArray["EMAIL"];
		$_SESSION["payerid"] = $resArray["PAYERID"];
		$_SESSION["amt"] = $resArray["AMT"];
		$_SESSION["desc"] = $resArray["L_PAYMENTREQUEST_0_DESC0"];
		$_SESSION["qty"] = $resArray["L_PAYMENTREQUEST_0_QTY0"];
		$_SESSION["key"] = $resArray["L_PAYMENTREQUEST_0_NAME0"];
	}
}
?>
