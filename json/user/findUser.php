<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;
$users = array();

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['query']) &&
		strlen($_GET['query']) >= 2) {
		$query = preg_replace('/[^A-Za-z0-9]/', ' ', $_GET['query']);
		$userList = UserHandler::search($query);
		
		if (!empty($userList)) {
			foreach ($userList as $userValue) {
				if ($userValue->isActivated() ||
					!$userValue->isActivated() && $user->hasPermission('*')) {
					array_push($users, array('id' => $userValue->getId(),
											 'firstname' => $userValue->getFirstname(),
											 'lastname' => $userValue->getLastname(),
											 'nickname' => $userValue->getNickname()));
				}
			}
			
			$result = true;
		} else {
			$message = 'Fant ingen resultater.';
		}
	} else {
		$message = 'Intet søkeord oppgitt.';
	}
}

echo json_encode(array('result' => $result, 'message' => $message, 'users' => $users));
?>