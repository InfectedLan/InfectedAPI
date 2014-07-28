<?php
require_once 'session.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	unset($_SESSION['user']);
	
	$result = true;
	$message = 'Du er nå logget ut.';
} else {
	$message = 'En feil oppstod!';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>