<?php
require_once 'session.php';
require_once 'mailmanager.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.sendEmails')) {
		if (isset($_GET['userList']) &&
			isset($_GET['subject']) &&
			isset($_GET['message']) &&
			is_array($_GET['userList']) &&
			!empty($_GET['subject']) &&
			!empty($_GET['message'])) {
			// Sends emails to users in userList with the given subject and message.
			MailManager::sendEmails($_GET['userList'], $_GET['subject'], $_GET['message']);
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