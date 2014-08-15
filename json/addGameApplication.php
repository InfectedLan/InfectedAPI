<?php
require_once 'handlers/gamehandler.php';
require_once 'handlers/gameapplicationhandler.php';

$result = false;
$message = null;

if (isset($_GET['name']) &&
	isset($_GET['tag']) &&
	isset($_GET['contactname']) &&
	isset($_GET['contactnick']) &&
	isset($_GET['phone']) &&
	isset($_GET['email']) &&
	isset($_GET['gameId']) &&
	!empty($_GET['name']) &&
	!empty($_GET['tag']) &&
	!empty($_GET['contactname']) &&
	!empty($_GET['contactnick']) &&
	is_numeric($_GET['phone']) &&
	!empty($_GET['email']) &&
	is_numeric($_GET['gameId'])) {
	$name = $_GET['name'];
	$tag = $_GET['tag'];
	$contactname = $_GET['contactname'];
	$contactnick = $_GET['contactnick'];
	$phone = $_GET['phone'];
	$email = $_GET['email'];
	$game = GameHandler::getGame($_GET['gameId']);

	GameApplicationHandler::createGameApplication($game, $name, $tag, $contactname, $contactnick, $phone, $email);

	$result = true;
} else {
	$message = 'Du har ikke fyllt ut alle feltene.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>