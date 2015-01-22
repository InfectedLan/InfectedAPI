<?php
require_once 'session.php';
require_once 'mailmanager.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.email')) {
		if (isset($_GET['userIdList']) &&
			isset($_GET['subject']) &&
			isset($_GET['message']) &&
			count($_GET['userIdList']) > 0 &&
			!empty($_GET['subject']) &&
			!empty($_GET['message'])) {
			$userIdList = explode(',', $_GET['userIdList']);
			$userList = array();
			
			// If the id's in user list is lower or equal to 0, we have to do something special here.
			if (count($userIdList) <= 1) {
				if ($userIdList[0] == 0) {
					$userList = UserHandler::getUsers();
				} else if ($userIdList[0] == -1) {
					$userList = UserHandler::getParticipantUsers(EventHandler::getCurrentEvent());
				}
			} else {
				// Build the userList from the given id's
				foreach ($userIdList as $userId) {
					array_push($userList, UserHandler::getUser($userId));
				}
			}
			
			// Sends emails to users in userList with the given subject and message.
			MailManager::sendEmails($userList, $_GET['subject'], $_GET['message']);
			
			// Format message differently when we're just sending email to one user.
			if (count($userList) <= 1) {
				$message = 'Din e-post ble sendt til den valgte brukeren.';
			} else {
				$message = 'Din e-post ble sendt til de valgte brukerene.';
			}
			
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