<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/chathandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['id'])) {
		$chat = ChatHandler::getChat($_GET['id']);

		if ($chat != null) {
			if (ChatHandler::isChatMember($user, $chat) || $user->hasPermission('*') || $user->hasPermission('compo.chat')) {
				$message = ChatHandler::getLastChatMessage($chat);
				
				if (isset($message)) {
					$result = array('id' => $message->getId(), 
									'message' => $message->getMessage(), 
									'user' => $message->getUser()->getNickname());
					
					//Tell chat if admin or not
					if ($user->hasPermission('*') || 
						$user->hasPermission('event.compo')) {
						$result['admin'] = true;
					} else {
						$result['admin'] = false;
					}
				} else {
					$result = array("id" => -1);
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