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
				
				if ($message != null) {
					$subject = $message->getUser();

					$result = array('id' => $message->getId(), 
									'user' => $subject->getNickname(),
									'time' => date('H:i:s', $message->getTime()),
									'message' => $message->getMessage());

					//Tell chat if admin or not
					if ($subject->hasPermission('*') || 
						$subject->hasPermission('compo.chat')) {
						$result['admin'] = true;
					} else {
						$result['admin'] = false;
					}
				} else {
					$result = array('id' => -1);
					$message = '<p>Chatten har ingen meldinger!</p>';
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