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

require_once 'handlers/userhandler.php';
require_once 'objects/user.php';

session_start();

/*
 * Used to get information from current sessions.
 */
class Session {
	/*
	 * Returns true if the current user is authenticated.
	 */
	public static function isAuthenticated() {
		// Check if we remember this user.
		return isset($_SESSION['userId']);
	}

	/*
	 * Returns true if the current user is a member (To clarify, is a crew member).
	 */
	public static function isMember() {
		return self::isAuthenticated() && self::getCurrentUser()->isGroupMember();
	}

	/*
	 * Returns the current user.
	 */
	public static function getCurrentUser() {
		if (self::isAuthenticated()) {
			return UserHandler::getUser($_SESSION['userId']);
		}
	}

  /*
   * Returns the user by the given session id.
   */
  public function getUserFromSessionId($sessionId) {
      $sessionData = exec("cat /var/lib/php5/sessions/sess_" . $sessionId); //I am not debugging regex at 0:35 in the morning, and it is temp anyways
      $regex = '/userId\|s:\d+:"(.+)";/';

      preg_match($regex, $sessionData, $matches);

      $id = $matches[1]; //$matches[0] returns the entire regex, $matches[1] returns the first subgroup.

      return UserHandler::getUser($id);
  }
}
?>
