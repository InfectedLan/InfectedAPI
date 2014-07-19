<?php
require_once 'database.php';

$database = new Database();
$mysql = new MySQL();

$action = isset($_GET['action']) ? $_GET['action'] : 0;
$id = isset($_GET['id']) ? $_GET['id'] : 0;

if (isset($_GET['action'])) {
	if ($action == 1) {
		if (isset($_POST['game']) &&
			isset($_POST['name']) &&
			isset($_POST['tag']) &&
			isset($_POST['contactname']) &&
			isset($_POST['contactnick']) &&
			isset($_POST['phone']) &&
			isset($_POST['email'])) {
			$mysql = new MySQL();
			$con = $mysql->open();
			
			$game = mysqli_real_escape_string($con, $_POST['game']);
			$name = mysqli_real_escape_string($con, $_POST['name']);
			$tag = mysqli_real_escape_string($con, $_POST['tag']);
			$contactname = mysqli_real_escape_string($con, $_POST['contactname']);
			$contactnick = mysqli_real_escape_string($con, $_POST['contactnick']);
			$phone = mysqli_real_escape_string($con, $_POST['phone']);
			$email = mysqli_real_escape_string($con, $_POST['email']);
			
			$database->createGameApplication($game, $name, $tag, $contactname, $contactnick, $phone, $email);
			$mysql->close($con);
			
			header('Location: ../index.php?viewPage=competition-game&gameId=' . $game . '&message=1');
			return;
		}
	}

	if (isset($_SESSION['username'])) {
		if (User::hasPermission($_SESSION['username'], 'admin') || User::hasPermission($_SESSION['username'], 'site-admin')) {
			if ($action == 2) {
				if (isset($_GET['id'])) {
					$database->removeGameApplication($id);
				}
			}
		}
	}
}

header('Location: ../');
?>