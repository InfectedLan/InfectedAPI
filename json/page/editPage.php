<?php
require_once 'session.php';
require_once 'handlers/pagehandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('admin.website')) {
		if (isset($_GET['id']) &&
			isset($_GET['title']) &&
			isset($_GET['content']) &&
			is_numeric($_GET['id']) &&
			!empty($_GET['title']) &&
			!empty($_GET['content'])) {
			$page = PageHandler::getPage($_GET['id']);
			$title = $_GET['title'];
			$content = $_GET['content'];
			
			if ($page != null) {
				PageHandler::updatePage($id, $title, $content);
				$result = true;
			} else {
				$message = '<p>Siden finnes ikke.</p>';
			}
		} else {
			$message = '<p>Du har ikke fyllt ut alle feltene.</p>';
		}
	} else {
		$message = '<p>Du har ikke tillatelse til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>