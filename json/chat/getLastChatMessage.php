<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/chathandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	if(isset($_GET['id'])) {
		$chat = ChatHandler::getChat($_GET['id']);

		if(isset($chat)) {
			if(ChatHandler::isChatMember($user, $chat) || $user->hasPermission('*') || $user->hasPermission('functions.compoadmin')) {
				$message = ChatHandler::getLastChatMessage($chat);
				if(isset($message)) {
					$result = array("id" => $message->getId(), "message" => $message->getMessage(), "user" => $message->getUser()->getDisplayName());
					//Tell chat if admin or not
					if($user->hasPermission('*') || $user->hasPermission('functions.compoadmin')) {
						$result['admin'] = true;
					} else {
						$result['admin'] = false;
					}
				} else {
					$result = array();
					$message = "Chatten har ingen meldinger!";
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