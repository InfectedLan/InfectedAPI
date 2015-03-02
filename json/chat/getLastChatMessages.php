/*
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

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

<?php
require_once 'session.php';
require_once 'handlers/chathandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id']) && 
		isset($_GET['count'])) {
		$chat = ChatHandler::getChat($_GET['id']);

		if ($chat != null) {
			if ($user->hasPermission('*') || 
			    $user->hasPermission('compo.chat') || 
			    ChatHandler::isChatMember($user, $chat) ||
				$chat->getId() == 1) {
				$messageList = $chat->getLastMessages($_GET['count']);
				$result = array();

				foreach ($messageList as $message) {
					$subject = $message->getUser();

					$toPush = array('id' => $message->getId(), 
									'user' => $subject->getNickname(),
									'time' => date('H:i:s', $message->getTime()),
									'message' => $message->getMessage());

					//Tell chat if admin or not
					if ($subject->hasPermission('*') || 
						$subject->hasPermission('compo.chat')) {
						$toPush['admin'] = true;
					} else {
						$toPush['admin'] = false;
					}

					array_push($result, $toPush);
				}
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

echo json_encode(array('result' => $result, 'message' => $message));
?>