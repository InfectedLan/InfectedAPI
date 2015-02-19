<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/clanhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['user']) && 
		isset($_GET['clan'])) {
		$kickUser = UserHandler::getUser($_GET['user']);
		
		if ($kickUser != null) {
			$clan = ClanHandler::getClan($_GET['clan']);
		
			if ($clan != null) {
				if ($user->equals($clan->getChief())) {
					$clan->kick($kickUser);
					$result = true;
				} else {
					$message = '<p>Du er ikke chief.</p>';
				}
			} else {
				$message = '<p>Clanen finnes ikke!</p>';
			}
		} else {
			$message = '<p>Brukeren du prøvde å kicke finnes ikke!</p>';
		}
	} else {
		$message = '<p>Vi mangler felt.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>