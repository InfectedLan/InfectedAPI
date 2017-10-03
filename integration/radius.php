<?php
include 'database.php';
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
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

require_once 'handlers/userhandler.php';

// Change the specific vlans here according to event's setup.
$vlan_noc = 200;
$vlan_crew_wireless = 50;

$vlan = $vlan_crew_wireless; // Default vlan to use.
$output = "Auth-Type = Reject";

if (isset($_GET['identifier']) &&
	isset($_GET['password']) &&
	!empty($_GET['identifier']) &&
	!empty($_GET['password'])) {
	$identifier = $_GET['identifier'];
	$password = hash('sha256', $_GET['password']);

	if (UserHandler::hasUser($identifier)) {
		$user = UserHandler::getUserByIdentifier($identifier);
		$storedPassword = $user->getPassword();

		if ($user->isActivated() && hash_equals($password, $storedPassword)) { // && $user->isGroupMember()
			if ($user->getId() == 2) { // TODO: Match tech crew here.
				$vlan = $vlan_noc;
			}

$output = <<<EOL
Auth-Type = Accept,
Tunnel-Type = VLAN,
Tunnel-Medium-Type = IEEE-802,
Tunnel-Private-Group-Id = $vlan
EOL;
		}
	}
}

header('Content-Type: text/plain');
echo $output;
Database::cleanup();
?>
