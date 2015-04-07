<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'session.php';
require_once 'paypal/callerservice.php';
require_once 'paypalsecret.php';

class PayPal {
	public static function getPaymentUrl($ticketType, $amount, $key, $user) {
		$currencyCodeType="NOK";
		$paymentType="Sale";

		//Set the return and cancel url
		/*$returnURL =urlencode('https://tickets.infected.no/v2/index.php?page=reviewOrder');
		$cancelURL =urlencode('https://tickets.infected.no/v2/index.php');*/

		//$returnURL = urlencode('https://tickets.infected.no/v2/index.php?page=reviewOrder');
		//$cancelURL = urlencode('https://tickets.infected.no/v2/index.php');

		//Calculate total price		   	
		$itemamt = $amount*$ticketType->getPriceForUser($user);
		$amt = $itemamt;
		$maxamt= $amt;
	   
		$nvpstr = "&_LITEMCATEGORY0=Digital&NOSHIPPING=1&L_NAME0=" . $ticketType->getHumanName() . "&L_AMT0=" . $ticketType->getPriceForUser($user) . 
		"&L_QTY0=" . $amount . "&MAXAMT=" . (string)$maxamt . "&AMT=" . (string)$amt . "&ITEMAMT=" . 
		(string)$itemamt . "&CALLBACKTIMEOUT=4&L_NUMBER0=10001&L_DESC0=" . $ticketType->getHumanName() . 
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
			return $url;
		} else  {
			return null;
		}
	}

	public static function completePurchase($token, $paymentAmount, $currCodeType, $payerID, $serverName) {
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
				return $transid;
			}
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