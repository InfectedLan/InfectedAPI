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
require_once 'handlers/tickettypehandler.php';
require_once 'objects/user.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['ticketType']) &&
		isset($_GET['amount']) &&
		is_numeric($_GET['ticketType']) &&
		is_numeric($_GET['amount'])) {
		$ticketType = TicketTypeHandler::getTicketType($_GET['ticketType']);
		$amount = $_GET['amount'];

		$message = 'Prisen er: ' . $ticketType->getPriceByUser($user, $amount);
		$result = true;
	} else {
		$message = "error 1";
	}
} else {
	$message = "error 2";
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>
