<?php
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

echo json_encode(array('result' => $result, 'message' => $message));
?>