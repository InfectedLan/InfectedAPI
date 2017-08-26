<?php
include 'database.php';
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
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	if (isset($_GET['id']) &&
		is_numeric($_GET['id'])) {
		$user = UserHandler::getUser($_GET['id']);
		
		if ($user != null) {
			if ($user->isEligibleForCompos()) {
				$result = true;
			} else {
				$message = '<p>Brukeren har ikke lov til å delta i compoer. Har brukeren en gyldig billett?</p>';
			}
		} else {
			$message = '<p>Brukeren eksisterer ikke!</p>';
		}
	} else {
		$message = '<p>Vi mangler felt.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

header('Content-Type: text/plain');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
?>