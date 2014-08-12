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
	
		if (isset($_GET['action'])) {
			$action = $_GET['action'];
			
			switch ($action) {
				case 1: // Add
					if (isset($_POST['title']) &&
						isset($_POST['description']) &&
						isset($_POST['leader']) &&
						!empty($_POST['title']) &&
						!empty($_POST['description'])) {
						$name = strtolower(str_replace(' ', '-', $_POST['title']));
						$title = $_POST['title'];
						$description = $_POST['description'];
						$leader = $_POST['leader'];
						
						GroupHandler::createGroup($name, $title, $description, $leader);
						$result = true;
					} else {
						$message = 'Du har ikke fyllt ut alle feltene!';
					}
					
					break;
				
				case 2: // Remove
					if (isset($_GET['id'])) {
						GroupHandler::removeGroup($_GET['id']);
						$result = true;
					} else {
						$message = 'Ingen gruppe spesifisert.';
					}
					
					break;
					
				case 3: // Change
					if (isset($_GET['id']) &&
						isset($_POST['title']) &&
						isset($_POST['description']) &&
						isset($_POST['leader']) &&
						!empty($_POST['title']) &&
						!empty($_POST['description'])) {
						$id = $_GET['id'];
						$name = strtolower(str_replace(' ', '-', $_POST['title']));
						$title = $_POST['title'];
						$description = $_POST['description'];
						$leader = $_POST['leader'];

						GroupHandler::updateGroup($id, $name, $title, $description, $leader);
						$result = true;
					} else {
						$message = 'Du har ikke fylt ut alle feltene.';
					}
					
					break;
			}
		} else {
			$message = 'Handlingen som skal utføres er ikke spesifisert.';
		}
	} else {
		$message = 'Du har ikke rettigheter til dette.';
	}
} else {
	$message = 'Du er ikke logget inn.';
}

echo json_encode(array('result' => $result, 'message' => $message));
		
	/* Add user to group 
	} else if ($action == 4) {
		if (isset($_GET['groupId']) &&
			isset($_POST['userId'])) {
				$user = $database->getUser($_POST['userId']);

				// Sets group to the specified groupId.
				$user->setGroup($groupId);
			}
	Removes user from any group 
	} else if ($action == 5) {
		if (isset($_GET['userId'])) {
			$user = $database->getUser($_GET['userId']);

			// Sets group to 0, which means none.
			$user->setGroup(0);
		}
	} */
?>