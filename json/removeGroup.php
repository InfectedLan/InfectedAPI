<?php
require_once 'session.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('leader.group') ||
		$user->hasPermission('admin') ||
		$user->isGroupMember() && $user->isGroupLeader()) {
		if (isset($_GET['id'])) {
			GroupHandler::removeGroup($_GET['id']);
			$result = true;
		} else {
			$message = 'Ingen gruppe spesifisert.';
		}
	} else {
		$message = "Du har ikke tillatelser til dette.";
	}
} else {
	$message = "Du er ikke logget inn.";
}

echo json_encode(array('result' => $result, 'message' => $message));

?>