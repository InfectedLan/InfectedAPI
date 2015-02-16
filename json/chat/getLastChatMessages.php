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
				$messageList = ChatHandler::getLastMessages($chat, $_GET['count']);
				$result = array();

				foreach ($messageList as $message) {
					$subject = $message->getUser();

					$toPush = array('id' => $message->getId(), 
									'user' => $subject->getNickname(),
									'time' => date('Y-m-d H:i:s', $message->getTime()),
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
				$message = "Du er ikke med i denne chatten!";
			}
		} else {
			$message = 'Chatten finnes ikke!';
		}
	} else {
		$message = 'Vi mangler felt';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>