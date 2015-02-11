<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/chathandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	if(isset($_GET['id']) && isset($_GET['count'])) {
		$chat = ChatHandler::getChat($_GET['id']);

		if(isset($chat)) {
			if(ChatHandler::isChatMember($user, $chat) || $user->hasPermission('*') || $user->hasPermission('compo.chat')) {
				$messages = ChatHandler::getLastMessages($chat, $_GET['count']);
				$result = array();

				foreach ($messages as $message) {
					$toPush = array("id" => $message->getId(), "message" => $message->getMessage(), "user" => $message->getUser()->getNickname());

					//Tell chat if admin or not
					if($user->hasPermission('*') || $user->hasPermission('compo.chat')) {
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