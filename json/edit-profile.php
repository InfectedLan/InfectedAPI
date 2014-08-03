<?php
require_once 'session.php';
require_once 'handlers/userhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if (isset($_POST['firstname']) &&
		isset($_POST['lastname']) &&
		isset($_POST['email']) &&
		isset($_POST['gender']) &&
		isset($_POST['birthday']) &&
		isset($_POST['birthmonth']) &&
		isset($_POST['birthyear']) &&
		isset($_POST['phone']) &&
		isset($_POST['address']) &&
		isset($_POST['postalcode']) &&
		isset($_POST['nickname']) &&
		!empty($_POST['firstname']) &&
		!empty($_POST['lastname']) &&
		!empty($_POST['email']) &&
		($_POST['gender'] == 0 || $_POST['gender'] == 1) &&
		!empty($_POST['birthday']) &&
		!empty($_POST['birthmonth']) &&
		!empty($_POST['birthyear']) &&
		!empty($_POST['phone']) &&
		!empty($_POST['address']) &&
		!empty($_POST['postalcode']) &&
		!empty($_POST['nickname'])) {
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];
		$gender = $_POST['gender'];
		$birthdate = date('Y-m-d', strtotime($_POST['birthyear'] . '-' . $_POST['birthmonth'] . '-' . $_POST['birthday'])); 
		$phone = $_POST['phone'];
		$address = $_POST['address'];
		$postalcode = $_POST['postalcode'];
		$nickname = $_POST['nickname'];
		
		UserHandler::updateUser($user->getId(),
								$firstname, 
								$lastname, 
								$user->getUsername(), 
								$user->getPassword(), 
								$email, 
								$birthdate, 
								$gender, 
								$phone, 
								$address, 
								$postalcode, 
								$nickname);
		
		$result = true;
		$message = 'Profilen din er nå blitt oppdatert.';
	} else {
		$message = 'Du har ikke fyllt ut alle feltene.';
	}
} else {
	$message = "Du er allerede logget inn!";
} 

echo json_encode(array('result' => $result, 'message' => $message));
?>