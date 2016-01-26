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
	private $creatorId;
	private $groupId;
	private $teamId;
	private $userId;
	private $title;
	private $content;
	private $secondsOffset;
	private $time;
	private $done;
	private $inProgress;

	/*
	 * Returns true if this note has a creator.
	 */
	public function hasCreatorUser() {
		return $this->creatorId > 0;
	}

	/*
	 * Returns the creator of this note.
	 */
	public function getCreatorUser() {
		return UserHandler::getUser($this->creatorId);
	}

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
			$group = $this->getGroup();

			if ($this->hasTeam()) {
				$team = $this->getTeam();

				if ($team->hasLeader()) {
					return $this->getTeam()->getLeader();
				}
			} else {
				if ($group->hasLeader()) {
					return $this->getGroup()->getLeader();
				}
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
		list($h, $m, $s) = explode (":", $this->time);

		return ($h * 3600) + ($m * 60);
	}

	/*
	 * Returns the start time of this note.
	 */
	public function getAbsoluteTime() {
		$event = EventHandler::getCurrentEvent();

		return (strtotime(date('Y-m-d', $event->getStartTime())) + $this->getSecondsOffset() + $this->getTime());
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

	/*
	 * Returns true if this note is in progress.
	 */
	public function isInProgress() {
		return $this->inProgress ? true : false;
	}

	/*
	 * Sets a note to be in progress or not.
	 */
	public function setInProgress($inProgress) {
		NoteHandler::updateNoteInProgress($this, $inProgress);
	}

	public function isExpired() {
		$event = EventHandler::getCurrentEvent();
		$eventDate = strtotime(date('Y-m-d', $event->getStartTime()));
		$delay = 30 * 60; // 30 minutes delay.

		return ($eventDate + $this->getSecondsOffset() + $this->getTime() + $delay) <= time();
	}

	/*
	 * Returns true if this note is private.
	 */
	public function isPrivate() {
		return !$this->hasGroup() && !$this->hasTeam() && $this->hasUser();
	}

	/*
	 * Returns true if this note is delagated to a user, this returns false if the note is private.
	 */
	public function isDelegated() {
		if ($this->hasGroup()) {
			if ($this->hasUser()) {
				return true;
			} else {
				if ($this->hasTeam()) {
					return true;
				}
			}
		}

		return false;
	}

	/*
	 * Returns true if the given user is user of this note.
	 */
	public function isUser(User $user) {
		if ($this->isPrivate()) {
			return true;
		}

		if ($this->hasUser()) {
			if ($user->equals($this->getUser())) {
				return true;
			}
		} else {
			if ($this->hasGroup()) {
				if ($this->hasTeam()) {
					$team = $this->getTeam();

					if ($team->isLeader($user)) {
						return true;
					}
				} else {
					$group = $this->getGroup();

					if ($group->isLeader($user)) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/*
	 * Returns true if this note has a owner.
	 */
	public function hasOwner() {
		if ($this->isPrivate()) {
			return true;
		}

		if ($this->hasGroup()) {
			$group = $this->getGroup();

			if ($this->hasTeam()) {
				$team = $this->getTeam();

				if ($team->hasLeader()) {
					return true;
				}
			} else {
				if ($group->hasLeader()) {
					return true;
				}
			}
		}

		return false;
	}

	/*
	 * Returns true if the given user is owner of this note.
	 */
	public function isOwner(User $user) {
		if ($this->isPrivate()) {
			return true;
		}

		if ($this->hasGroup()) {
			$group = $this->getGroup();

			if ($group->isLeader($user) ||
				$group->isCoLeader($user)) {
				return true;
			}

			if ($this->hasTeam()) {
				$team = $this->getTeam();

				if ($team->isLeader($user)) {
					return true;
				}
			}
		}

		return false;
	}

	/*
	 * Returns true if the given user is watching this note.
	 */
	public function isWatching(User $user) {
		return NoteHandler::isWatchingNote($this, $user);
	}

	/*
	 * Returns a list of users watching this note.
	 */
	public function getWatchingUsers() {
		return NoteHandler::getWatchingUsers($this);
	}
}
?>
