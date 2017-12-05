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

require_once 'session.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/teamhandler.php';
require_once 'objects/databaseobject.php';

class Team extends DatabaseObject {
	private $groupId;
	private $name;
	private $title;
	private $description;
	private $active;

	/*
	 * Returns the group for this team.
	 */
	public function getGroup(): Group {
		return GroupHandler::getGroup($this->groupId);
	}

	/*
	 * Returns the name of this team.
	 */
	public function getName(): string {
		return $this->name;
	}

	/*
	 * Returns the title of this team.
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/*
	 * Return the description for this team.
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/*
	 * Return true if this team is currently active.
	 */
	public function isActive(): bool {
		return $this->active ? true : false;
	}

	/*
	 * Returns if this team has a leader.
	 */
	public function hasLeader(Event $event = null): bool {
		return TeamHandler::hasTeamLeader($this, $event);
	}

	/*
	 * Returns the leader of this team.
	 */
	public function getLeader(Event $event = null): User {
		return TeamHandler::getTeamLeader($this, $event);
	}

	/*
	 * Return true if the specified user is member of this team.
	 */
	public function isMember(User $user, Event $event = null): bool {
		return TeamHandler::isTeamMemberOf($user, $this, $event);
	}

	/*
	 * Return true if the specified user is leader of this team.
	 */
	public function isLeader(User $user, Event $event = null): bool {
		return TeamHandler::isTeamLeaderOf($user, $this, $event);
	}

	/*
	 * Returns an array of users that are members of this group.
	 */
	public function getMembers(Event $event = null): array {
		return TeamHandler::getTeamMembers($this, $event);
	}
}
?>
