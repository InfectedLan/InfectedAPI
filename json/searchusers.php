<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;
$key = null;
$users = array();

if (Session::isAuthenticated()) {
	if (isset($_GET['query']) &&
		isset($_GET['key']) && 
		strlen($_GET['query']) > 2) {
		$userList = UserHandler::search($_GET['query']);

		if (!empty($userList)) {
			$result = true;
			$key = htmlspecialchars($_GET['key']);
		
			foreach ($userList as $user) {
				array_push($users, array('firstname' => $user->getFirstname(), 'lastname' => $user->getLastname(), 'nickname' => $user->getNickname(), 'userId' => $user->getId()));
			}
		} else {
			$message = 'Ingen brukere ble funnet.';
		}	
	} else {
		$message = 'Spørring må være mer en 2 bokstaver lang.';
	}
} else {
	$message = 'Du er ikke logget inn!"}';
}

echo json_encode(array('result' => $result, 'message' => $message, 'key' => $key, 'users' => $users));
?>