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

class UserUtils {
	public static function toUserIdList(array $userList) {
		$userIdList = array();

		foreach ($userList as $user) {
			array_push($userIdList, $user->getId());
		}

		return $userIdList;
	}

	public static function fromUserIdList(array $userIdList) {
		$userList = array();

		foreach ($userIdList as $userId) {
			array_push($userList, UserHandler::getUser($userId));
		}

		return $userList;
	}
}
?>
