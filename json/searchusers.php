<?php
require_once 'utils.php';
require_once 'handlers/userhandler.php';

if (!Utils::isAuthenticated()) {
	echo '{"result":false, "message":"You aren\'t authenticated!"}';
	return;
}

if (!isset( $_GET['query'] ) || strlen($_GET['query']) < 2) {
	echo '{"result":false}';
	return;
}

$list = UserHandler::search($_GET['query']);

if(!isset($list)) {
	echo '{"result":false}';
} else {
	echo '{"result":true, "key":"' . htmlspecialchars($_GET['key']) . '", "users":[';
	
	$first = true;
	
	foreach ($list as $user) {
		if ($first==false) {
			echo ',';
		}
		
		echo '{"firstname":"' . $user->getFirstname() . '", "lastname":"' . $user->getLastname() . '", "nick":"' . $user->getNickname() . '", "userId":"' . $user->getId() . '"}';
		$first = false;
	}
	
	echo ']}';
}
?>