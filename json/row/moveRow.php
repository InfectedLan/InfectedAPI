<?php
require_once 'session.php';
require_once 'handlers/rowhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.seatmap')) {		
		if (isset($_GET['row'])) {
			$row = RowHandler::getRow($_GET['row']);
			
			if ($row != null) {
				if (isset($_GET['x']) && 
				    isset($_GET['y'])) {
					RowHandler::updateRow($row,  $_GET['x'], $_GET['y']);

					$result = true;
				} else {
					$message = '<p>Posisjonen er ikke satt!</p>';
				}
			} else {
				$message = '<p>Raden eksisterer ikke!</p>';
			}
		} else {
			$message = '<p>Raden er ikke satt!</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til å flytte en rad!</p>';
	}
} else {
	$message = '<p>Du må logge inn først!</p>';
}

if ($result) {
	echo json_encode(array('result' => $result));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>