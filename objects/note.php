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

require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'handlers/userhandler.php';
require_once 'objects/eventobject.php';

class Note extends EventObject {
	private $groupId;
	private $teamId;
	private $userId;
	private $content;
	private $deadlineTime;
	private $done;

	/*
	 * Returns true if this note has a group.
	 */
	public function hasGroup() {
		return $this->groupId > 0;
	}

	/*
	 * Returns the group of this note.
	 */
	public function getGroup() {
		return GroupHandler::getGroup($this->groupId);
	}

	/*
	 * Returns true if this note has a team.
	 */
	public function hasTeam() {
		return $this->teamId > 0;
	}

	/*
	 * Returns the team of this note.
	 */
	public function getTeam() {
		return TeamHandler::getTeam($this->teamId);
	}

	/*
	 * Returns true if this note has a user.
	 */
	public function hasUser() {
		return $this->userId > 0;
	}

	/*
	 * Returns the user of this note.
	 */
	public function getUser() {
		if ($this->hasGroup() && !$this->hasUser()) {
			if ($this->hasTeam()) {
				return $this->getTeam()->getLeader();
			} else {
				return $this->getGroup()->getLeader();
			}
		}

		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the content of this note.
	 */
	public function getContent() {
		return $this->content;
	}

	/*
	 * Returns the deadlineTime of this note.
	 */
	public function getDeadlineTime() {
		return strtotime($this->deadlineTime);
	}

	/*
	 * Returns the otificationTimeBeforeOffset of this note.
	 */
	public function getNotificationTimeBeforeOffset() {
		return $this->notificationTimeBeforeOffset;
	}

	/*
	 * Returns true if this note is done.
	 */
	public function isDone() {
		return $this->done ? true : false;
	}

	public function isDelegated() {
		return ($this->hasGroup() || ($this->hasGroup() && $this->hasTeam())) && $this->hasUser();
	}

	public function isUser(User $user) {
		if ($this->hasGroup() && !$this->hasUser()) {
			if ($this->hasTeam()) {
				return $this->getUser()->equals($this->getTeam()->getLeader());
			} else {
				return $this->getUser()->equals($this->getGroup()->getLeader());
			}
		}

		return $this->getUser()->equals($user);
	}

	public function isAdmin(User $user) {
		return ($this->hasGroup() && ($user->isGroupLeader() || $user->isGroupCoLeader())) ||
					 (($this->hasGroup() && $this->hasTeam()) && ($user->isTeamMember() && $user->isTeamLeader())) ||
					 (!$this->hasGroup() && !$this->hasTeam() && $this->hasUser());
	}

	/*
	 * Returns true if this note is done.
	 */
	public function setNotified($notified) {
		NoteHandler::updateNoteNotified($this, $notified);
	}
}
?>
