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
require_once 'objects/user.php';

/*
 * Used to store information about a group.
 */
class Group extends DatabaseObject {
	private $name;
	private $title;
	private $description;
	private $leaderId;
	private $active;
	private $queuing;

	/*
	 * Returns the name of this group.
	 */
	public function getName(): string {
		return $this->name;
	}

	/*
	 * Returns the title of this group.
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/*
	 * Returns the description of this group.
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/*
	 * Return true if this group is currently active.
	 */
	public function isActive(): bool {
		return $this->active ? true : false;
	}

	/*
	 * Return true if new applications for this group automatically should be queued.
	 */
	public function isQueuing(): bool {
		return $this->queuing ? true : false;
	}

	/*
	 * Returns if this group has a leader.
	 */
	public function hasLeader(Event $event = null): bool {
		return GroupHandler::hasGroupLeader($this, $event);
	}

	/*
	 * Returns the user which is the leader of this group.
	 */
	public function getLeader(Event $event = null): User {
		return GroupHandler::getGroupLeader($this, $event);
	}

	/*
	 * Return true if the specified user is member of this group.
	 */
	public function isMember(User $user, Event $event = null): bool {
		return GroupHandler::isGroupMemberOf($user, $this, $event);
	}

	/*
	 * Return true if the specified user is leader of this group.
	 */
	public function isLeader(User $user, Event $event = null): bool {
		return GroupHandler::isGroupLeaderOf($user, $this, $event);
	}

	/*
	 * Returns an array of users that are member of this group.
	 */
	public function getMembers(Event $event = null): array {
		return GroupHandler::getGroupMembers($this, $event);
	}

	/*
	 * Returns an array of all teams connected to this group.
	 */
	public function getTeams(Event $event = null): array {
		return TeamHandler::getTeamsByGroup($this, $event);
	}
}
?>
