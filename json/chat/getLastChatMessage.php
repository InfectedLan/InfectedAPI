<?php
require_once 'session.php';
require_once 'handlers/chathandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['id'])) {
		$chat = ChatHandler::getChat($_GET['id']);

		if ($chat != null) {
			if ($user->hasPermission('*') || 
				$user->hasPermission('compo.chat') || 
				ChatHandler::isChatMember($user, $chat) || 
				$chat->getId() == 1) {
				$message = ChatHandler::getLastChatMessage($chat);
				
				if ($messag != null) {
					$subject = $message->getUser();

					$result = array('id' => $message->getId(), 
									'user' => $subject->getNickname(),
									'time' => date('Y-m-d H:i:s', $message->getTime()),
									'message' => $message->getMessage());

					//Tell chat if admin or not
					if ($subject->hasPermission('*') || 
						$subject->hasPermission('compo.chat')) {
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