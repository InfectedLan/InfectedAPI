<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
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
require_once 'localization.php';
require_once 'handlers/chathandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id']) &&
		isset($_GET['count'])) {
		$chat = ChatHandler::getChat($_GET['id']);

		if ($chat != null) {
			if ($user->hasPermission('compo.chat') ||
				ChatHandler::isChatMember($user, $chat) ||
				$chat->getId() == 1) {
				$messageList = $chat->getLastMessages($_GET['count']);
				$result = [];

				foreach ($messageList as $message) {
					$subject = $message->getUser();

					$toPush = ['id' => $message->getId(),
										 'user' => $subject->getNickname(),
										 'time' => date('H:i:s', $message->getTime()),
										 'message' => $message->getMessage()];

					//Tell chat if admin or not
					if ($subject->hasPermission('*') ||
						$subject->hasPermission('compo.chat')) {
						$toPush['admin'] = true;
					} else {
						$toPush['admin'] = false;
					}

					$result[] = $toPush;
				}
			} else {
				$message = Localization::getLocale('you_are_not_a_participant_of_this_chat');
			}
		} else {
			$message = Localization::getLocale('this_chat_does_not_exist');
		}
	} else {
		$message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
	}
} else {
	$message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();
?>
