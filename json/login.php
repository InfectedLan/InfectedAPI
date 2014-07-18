<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/handlers/userhandler.php';

$result = true;
$message = "";

if (!Utils::isAuthenticated()) {
	if (isset($_POST['username']) &&
			isset($_POST['password']) &&
			!empty($_POST['username']) &&
			!empty($_POST['password'])) 
	{
		$username = $_POST['username'];
		$password = hash('sha256', $_POST['password']);

		if (UserHandler::userExists($username)) 
		{
			$user = UserHandler::getUserByName($username);
			$storedPassword = $user->getPassword();
			
			if ($password == $storedPassword) {
				$_SESSION['user'] = $user;
				$message = 'Du er nå logget inn!';
			}
			else
			{
				$result = false;
				$message = 'Feil brukernavn eller passord.';
			}
		} 
		else 
		{
			$result = false;
			$message = 'Feil brukernavn eller passord.';
		}
	}
	else
	{
		$result = false;
		$message = "Du har ikke skrevet inn et brukernavn og passord.";
	}
}
else
{
	$result = false;
	$message = "Du er allerede logget inn!";
}
echo '{"result":' . ($result ? 'true' : 'false') . ', "message":"' . $message . '"}';
?>