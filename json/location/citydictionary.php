<?php
require_once 'handlers/citydictionary.php';

$result = false;
$message = null;

if (isset($_GET['postalcode']) &&
	is_numeric($_GET['postalcode'])) {
	$city = CityDictionary::getCity($_GET['postalcode']);
	
	if ($city != null) {
		$result = true;
		$message = $city;
	} else {
		$result = true;
		$message = '<p>Ikke funnet.</p>';
	}
} else {
	$message = '<p>Postnummer ikke spesifisert.</p>';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>