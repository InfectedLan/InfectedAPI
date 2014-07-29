<?php
	require_once 'session.php';
	require_once 'handlers/tickethandler.php';

	$result = false;
	$message = null;

	if (Session::isAuthenticated()) {
		$user = Session::getCurrentUser();
		
	}

?>