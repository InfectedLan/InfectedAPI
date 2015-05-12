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
		return self::isRemembered();

		//return isset($_SESSION['user']);
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
			return $_SESSION['user'];
		}
	}
	
	/*
	 * Returns true if the current user is remembered.
	 */
	public static function isRemembered() {
		// Check if the cookie exists
		echo 'Identifier is:...';

		if (isset($_COOKIE['rememberUser'])) {
		    parse_str($_COOKIE['rememberUser']);
		 	
		    echo 'Identifier is: ' . $identifier;
		    echo 'Password is: ' . $password;

		    if (UserHandler::hasUser($identifier)) {
				$user = UserHandler::getUserByIdentifier($identifier);
				$storedPassword = $user->getPassword();
			
				if (hash_equals($password, $storedPassword)) {
					echo 'Passordene er faktisk like.';

					if ($user->isActivated()) {
				        //$_SESSION['user'] = $user;

				        return true;
				    }
				}
			}
		}

		return false;
	}

	/*
	 * Reloads the current user from database.
	 */
	public static function reload() {
		if (self::isAuthenticated()) {
			$user = self::getCurrentUser();
			
			unset($_SESSION['user']);
			$_SESSION['user'] = UserHandler::getUser($user->getId());;
		}
	}
}
?>