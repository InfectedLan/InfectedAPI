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
				if (RowHandler::safeToDelete($row)) {
					RowHandler::removeRow($row);
					$result = true;
				} else {
					$message = '<p>Noen sitter på raden du prøver å slette!</p>';
				}
			} else {
				$message = '<p>Raden eksisterer ikke!</p>';
			}
		} else {
			$message = '<p>Raden er ikke satt!</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til å legge til en rad!</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

if ($result) {
	echo json_encode(array('result' => $result));
} else {
	echo json_encode(array('result' => $result, 'message' => $message));
}
?>