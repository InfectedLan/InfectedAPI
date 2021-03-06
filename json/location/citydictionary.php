<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2017 Infected <http://infected.no/>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3.0 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'database.php';
require_once 'localization.php';
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
		$message = Localization::getLocale('not_found');
	}
} else {
	$message = Localization::getLocale('no_postcode_specified');
}

header('Content-Type: application/json');
echo json_encode(['result' => $result, 'message' => $message], JSON_PRETTY_PRINT);
Database::cleanup();
?>
