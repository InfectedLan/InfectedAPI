<?php
require_once 'session.php';
require_once 'mailmanager.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.email')) {
		if (isset($_GET['user']) &&
			isset($_GET['subject']) &&
			isset($_GET['message']) &&
			is_numeric($_GET['user']) &&
			!empty($_GET['subject']) &&
			!empty($_GET['message'])) {
			$to = UserHandler::getUser($_GET['user']);
			// Sends emails to users in userList with the given subject and message.
			MailManager::sendEmails($to, $_GET['subject'], $_GET['message']);
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