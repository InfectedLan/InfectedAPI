<?php
require_once 'handlers/citydictionary.php';

$result = false;
$message = null;

if (isset($_GET['postalcode'])) {
	$city = CityDictionary::getCity($_GET['postalcode']);
	
	if ($city != null) {
		$result = true;
		$message = $city;
	} else {
		$result = true;
		$message = 'Ikke funnet.';
	}
} else {
	$message = 'Postnummer ikke spesifisert.';
}

echo json_encode(array('result' => $result, 'message' => $message));
?>