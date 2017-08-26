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
require_once 'localization.php';
require_once 'handlers/chathandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if (isset($_GET['id'])) {
		$chat = ChatHandler::getChat($_GET['id']);

		if ($chat != null) {
			if ($user->hasPermission('compo.chat') ||
				ChatHandler::isChatMember($chat, $user) ||
				$chat->getId() == 1) {
				$message = $chat->getLastMessage();

				if ($message != null) {
					$subject = $message->getUser();

					$result = ['id' => $message->getId(),
										 'user' => $subject->getNickname(),
										 'time' => date('H:i:s', $message->getTime()),
										 'message' => $message->getMessage()];

					//Tell chat if admin or not
					if ($subject->hasPermission('*') ||
						$subject->hasPermission('compo.chat')) {
						$result['admin'] = true;
					} else {
						$result['admin'] = false;
					}
				} else {
					$result = ['id' => -1];
					$message = Localization::getLocale('this_chat_does_not_contain_any_messages');
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

header('Content-Type: text/plain');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
?>
