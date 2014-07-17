<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/UserHandler.php';

if (Utils::isAuthenticated() == false) {
	echo '{"result":false, "message":"You arent authenticated!"}';
	return;
}

if (!isset( $_GET["query"] ) || strlen( $_GET["query"] ) < 2) {
	echo '{"result":false}';
	return;
}

$list = UserHandler::searchUsers($_GET["query"]);

if(!isset($list)) {
	echo '{"result":false}';
} else {
	echo '{"result":true, "key":"' . htmlspecialchars($_GET["key"]) . '", "users":[';
	
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