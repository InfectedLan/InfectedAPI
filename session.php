<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
		return isset($_SESSION['user']);
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