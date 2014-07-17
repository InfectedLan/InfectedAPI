<?php
	require_once '/../utils.php';
	require_once '/../handlers/UserHandler.php';
	$list = UserHandler::searchUsers($_GET["query"]);

	if(!isset($list))
	{
		echo '{"result":false}';
	}
	else
	{
		echo '{"result":true, "users":[';
		$first = true;
		foreach ($list as $user) {
			if($first==false)
			{
				echo ',';
			}
			echo '{"firstname":"' . $user->getFirstname() . '", "lastname":"' . $user->getLastname() . '", "nick":"' . $user->getNickname() . '"}';
			$first = false;
		}
		echo ']}';
	}
?>