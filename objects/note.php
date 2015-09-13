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
require_once 'handlers/eventhandler.php';
require_once 'objects/eventobject.php';

class Note extends EventObject {
	private $groupId;
	private $teamId;
	private $userId;
	private $title;
	private $content;
	private $secondsOffset;
	private $time;
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
	 * Returns the title of this note.
	 */
	public function getTitle() {
		return $this->title;
	}

	/*
	 * Returns the content of this note.
	 */
	public function getContent() {
		return $this->content;
	}

	/*
	 * Returns the secondsOffset of this note.
	 */
	public function getSecondsOffset() {
		return $this->secondsOffset;
	}

	/*
	 * Returns the time of this note.
	 */
	public function getTime() {
		return strtotime($this->time);
	}

	/*
	 * Returns the start time of this note.
	 */
	public function getAbsoluteTime() {
		$event = EventHandler::getCurrentEvent();

		return (strtotime(date('Y-m-d', $event->getStartTime())) + $this->getSecondsOffset()) + ($this->getTime() - strtotime('today'));
	}

	/*
	 * Returns true if this note is done.
	 */
	public function setNotified($notified) {
		NoteHandler::updateNoteNotified($this, $notified);
	}

	/*
	 * Returns true if this note is done.
	 */
	public function isDone() {
		return $this->done ? true : false;
	}

	/*
	 * Sets a note to be done or not.
	 */
	public function setDone($done) {
		NoteHandler::updateNoteDone($this, $done);
	}

	public function isExpired() {
		$event = EventHandler::getCurrentEvent();

		return ($event->getStartTime() + $this->getSecondsOffset()) <= time();
	}

	public function isPrivate() {
		return !$this->hasGroup() && !$this->hasTeam() && $this->hasUser();
	}

	public function isDelegated() {
		return ($this->hasGroup() || ($this->hasGroup() && $this->hasTeam())) && $this->hasUser();
	}

	public function isUser(User $user) {
		if ($this->hasGroup() && !$this->hasUser()) {
			$group = $this->getGroup();

			if ($group->hasLeader()) {
				return $this->getUser()->equals($group->getLeader());
			} else if ($this->hasTeam()) {
				$team = $this->getTeam();

				if ($team->hasLeader()) {
					return $this->getUser()->equals($team->getLeader());
				}
			}
		}

		return $this->getUser()->equals($user);
	}

	public function isOwner(User $user) {
		return ($this->hasGroup() && ($user->isGroupLeader() || $user->isGroupCoLeader())) ||
					 (($this->hasGroup() && $this->hasTeam()) && ($user->isTeamMember() && $user->isTeamLeader())) ||
					 $this->isPrivate();
	}
}
?>
