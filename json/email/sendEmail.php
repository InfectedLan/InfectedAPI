<?php
require_once 'session.php';
require_once 'mailmanager.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.email')) {
		if (isset($_GET['userIdList']) &&
			isset($_GET['subject']) &&
			isset($_GET['message']) &&
			!empty($_GET['subject']) &&
			!empty($_GET['message'])) {
			$userIdList = explode(',', $_GET['userIdList']);
			$userList = array();
			
			// If the list of user contains "0", that means send it to anyone.
			if (in_array(0, $userIdList)) {
				$userList = UserHandler::getUsers();
			} else {
				// Build the userList from the given id's
				foreach ($userIdList as $userId) {
					array_push($userList, UserHandler::getUser($userId));
				}
			}
			
			// Sends emails to users in userList with the given subject and message.
			MailManager::sendEmails($userList, $_GET['subject'], $_GET['message']);
			$message = 'Din e-post ble sendt til de oppgitte mottakeren.';
			$result = true;
		} else {
			$message = 'Mangler informasjon, sjekk at du har fylt ut alle feltene.';
		}
	} else {
		$message = 'Du har ikke tilgang til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>