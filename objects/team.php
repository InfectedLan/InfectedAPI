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

require_once 'session.php';
require_once 'handlers/grouphandler.php';
require_once 'handlers/userhandler.php';
require_once 'handlers/teamhandler.php';
require_once 'objects/eventobject.php';

class Team extends EventObject {
	private $groupId;
	private $name;
	private $title;
	private $description;
	private $leaderId;

	/*
	 * Returns the group for this team.
	 */
	public function getGroup() {
		return GroupHandler::getGroupByEvent($this->getEvent(), $this->groupId);
	}

	/*
	 * Returns the name of this team.
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Returns the title of this team.
	 */
	public function getTitle() {
		return $this->title;
	}

	/*
	 * Return the description for this team.
	 */
	public function getDescription() {
		return $this->description;
	}

	/*
	 * Returns if this team has a leader.
	 */
	public function hasLeader() {
		return TeamHandler::hasTeamLeader($this);
	}

	/*
	 * Returns the leader of this team.
	 */
	public function getLeader() {
		return UserHandler::getUser($this->leaderId);
	}

	/*
	 * Returns an array of users that are members of this group.
	 */
	public function getMembers() {
		return TeamHandler::getMembersByEvent($this->getEvent(), $this);
	}
}
?>
