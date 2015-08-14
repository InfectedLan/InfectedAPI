<?php
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
require_once '../session.php';

class AuthenticatedUser {
	private $infectedUser;
	private $session;
	function ___construct($session, $user) {
		$this->infectedUser = $user;
		$this->session = $session;
	}
	static function AuthenticateUser($session, $sessionid) {
		echo "Authenticating session id " . $sessionid . "\n";
		echo "session data: " . exec("cat /var/lib/php5/sessions/sess_" . $sessionid) . "\n";
		session_id($sessionid);
		//session_start();
		$user = Session::getCurrentUser();
		echo "Authented user " . $user->getUsername();
	}
}
?>