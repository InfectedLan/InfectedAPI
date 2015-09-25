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
require_once 'objects/user.php';

/*
 * Used to store information about a group.
 */
class Group extends EventObject {
	private $name;
	private $title;
	private $description;
	private $leaderId;
	private $coleaderId;
	private $queuing;

	/*
	 * Returns the name of this group.
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Returns the title of this group.
	 */
	public function getTitle() {
		return $this->title;
	}

	/*
	 * Returns the description of this group.
	 */
	public function getDescription() {
		return $this->description;
	}

	/*
	 * Returns if this group has a leader.
	 */
	public function hasLeader() {
		return $this->leaderId > 0;
	}

	/*
	 * Returns the user which is the leader of this group.
	 */
	public function getLeader() {
		return UserHandler::getUser($this->leaderId);
	}

	/*
	 * Returns if this group has a co-leader.
	 */
	public function hasCoLeader() {
		return $this->coleaderId > 0;
	}

	/*
	 * Returns the user which is the co-leader of this group.
	 */
	public function getCoLeader() {
		return UserHandler::getUser($this->coleaderId);
	}

	/*
	 * Return true if new applications for this group automatically should be queued.
	 */
	public function isQueuing() {
		return $this->queuing ? true : false;
	}

	/*
	 * Return true if the specified user is member of this group.
	 */
	public function isMember(User $user) {
		return $user->isGroupMember() && $this->equals($user->getGroup());
	}

	/*
	 * Return true if the specified user is leader of this group.
	 */
	public function isLeader(User $user) {
		return $this->hasLeader() && $user->equals($this->getLeader());
	}

	/*
	 * Return true if the specified user is co-leader of this group.
	 */
	public function isCoLeader(User $user) {
		return $this->hasCoLeader() && $user->equals($this->getCoLeader());
	}

	/*
	 * Returns an array of users that are member of this group.
	 */
	public function getMembers() {
		return GroupHandler::getMembersByEvent($this->getEvent(), $this);
	}

	/*
	 * Returns an array of all teams connected to this group.
	 */
	public function getTeams() {
		return TeamHandler::getTeamsByEventAndGroup($this->getEvent(), $this);
	}
}
?>
