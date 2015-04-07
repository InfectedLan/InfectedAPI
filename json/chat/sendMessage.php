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
require_once 'handlers/chathandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['id']) && 
		isset($_GET['message'])) {
		$chat = ChatHandler::getChat($_GET['id']);

		if ($chat != null) {
			if ($user->hasPermission('*') || 
				$user->hasPermission('compo.chat') ||
				$chat->isMember($user)) {
				$chat->sendMessage($user, $_GET['message']);
				$result = true;
			} else {
				$message = '<p>Du er ikke med i denne chatten!</p>';
			}
		} else {
			$message = '<p>Chatten finnes ikke!</p>';
		}
	} else {
		$message = '<p>Vi mangler felt.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>