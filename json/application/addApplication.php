<?php
require_once 'session.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/applicationhandler.php';
require_once 'handlers/grouphandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	// Only allow non-members to apply.
	if (!$user->isGroupMember()) {
		
		// Check that the user has an cropped avatar.
		if ($user->hasCroppedAvatar()) {
			
			if (isset($_GET['groupId']) &&
				isset($_GET['content']) &&
				is_numeric($_GET['groupId']) &&
				!empty($_GET['content'])) {
				$group = GroupHandler::getGroup($_GET['groupId']);
				$content = $_GET['content'];
				
				if (!ApplicationHandler::hasUserApplicationForGroup($user, $group)) {
					ApplicationHandler::createApplication($group, $user, $content);
					$result = true;
					$message = 'Din søknad til crewet "' . $group->getTitle() . '" er nå sendt.';
				} else {
					$message = 'Du har allerede søkt til ' . $group->getTitle() . ' crew. Du kan søke igjen hvis søknaden din skulle bli avslått.';
				}
			} else {
				$message = 'Du har ikke fyllt ut alle feltene.';
			}
		} else {
			$message = "Du må laste opp en avatar før du kan søke!";
		}
	} else {
		$message = 'Du er allerede med i et crew.';
	}
} else {
	$message = 'Du er allerede logget inn!';
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>