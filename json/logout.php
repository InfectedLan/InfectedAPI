<?php
require_once 'session.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	unset($_SESSION['user']);
	
	$result = true;
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>