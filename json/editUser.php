<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_GET['firstname']) &&
		isset($_GET['lastname']) &&
		isset($_GET['gender']) &&
		isset($_GET['birthday']) &&
		isset($_GET['birthmonth']) &&
		isset($_GET['birthyear']) &&
		isset($_GET['phone']) &&
		isset($_GET['address']) &&
		isset($_GET['postalcode']) &&
		isset($_GET['nickname']) &&
		!empty($_GET['firstname']) &&
		!empty($_GET['lastname']) &&
		is_numeric($_GET['gender']) &&
		is_numeric($_GET['birthday']) &&
		is_numeric($_GET['birthmonth']) &&
		is_numeric($_GET['birthyear']) &&
		is_numeric($_GET['phone']) &&
		!empty($_GET['address']) &&
		is_numeric($_GET['postalcode'])) {
		$firstname = $_GET['firstname'];
		$lastname = $_GET['lastname'];
		$gender = $_GET['gender'];
		$birthdate = $_GET['birthyear'] . '-' . $_GET['birthmonth'] . '-' . $_GET['birthday']; 
		$phone = $_GET['phone'];
		$address = $_GET['address'];
		$postalcode = $_GET['postalcode'];
		$nickname = empty($_GET['nickname']) ? $user->getUsername() : $_GET['nickname'];
		
		UserHandler::updateUser($user->getId(),
								$firstname, 
								$lastname, 
								$user->getUsername(), 
								$user->getPassword(), 
								$user->getEmail(), 
								$birthdate, 
								$gender, 
								$phone, 
								$address, 
								$postalcode, 
								$nickname);
		
		// Update the user instance form database.
		Session::reload();
		$result = true;
	} else {
		$message = 'Du har ikke fyllt ut alle feltene.';
	}
} else {
	$message = "Du er allerede logget inn!";
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>