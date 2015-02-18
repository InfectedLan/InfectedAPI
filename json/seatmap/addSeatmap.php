<?php
require_once 'session.php';
require_once 'handlers/seatmaphandler.php';

$result = false;
$message = null;
$id = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.seatmap')) {
		if (isset($_GET['name'])) {
			$seatmap = SeatmapHandler::createNewSeatmap($_GET['name'], 'default.png');
			$result = true;
			$id = $seatmap->getId();
		} else {
			$message = '<p>Navn er ikke satt!</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til å lage et seatmap</p>!';
	}
} else {
	$message = '<p>Du må logge inn først!</p>';
}

if ($result) {
	echo json_encode(array('result' => $result, 'id' => $id));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>