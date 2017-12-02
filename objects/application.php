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

require_once 'handlers/grouphandler.php';
require_once 'handlers/userhandler.php';
require_once 'objects/eventobject.php';

class Application extends EventObject {
	private $groupId;
	private $userId;
	private $openedTime;
	private $closedTime;
	private $state;
	private $content;
	private $updatedByUserId;
	private $comment;

	/*
	 * Returns the group that this application is for.
	 */
	public function getGroup(): Group {
		return GroupHandler::getGroup($this->groupId);
	}

	/*
	 * Returns the user which opened this application.
	 */
	public function getUser(): User {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the time when this application was opened.
	 */
	public function getOpenedTime(): int {
		return strtotime($this->openedTime);
	}

	/*
	 * Returns the time when this application was closed.
	 */
	public function getClosedTime(): int {
		return strtotime($this->closedTime);
	}

	/*
	 * Returns the state of this application.
	 */
	public function getState(): int {
		return $this->state;
	}

	/*
	 * Returns the state of this application.
	 */
	public function getStateAsString(): string {
		$updatedByUser = $this->getUpdatedByUser();

		if ($this->isQueued()) {
			return 'Står i kø'; // TODO: Add localization for this.
		} else {
			switch ($this->getState()) {
				case 1:
					return 'Ikke behandlet'; // TODO: Add localization for this.
					break;

				case 2:
					return 'Godkjent' . ($updatedByUser != null ? ' av ' . $updatedByUser->getDisplayName() : null); // TODO: Add localization for this.
					break;

				case 3:
					return 'Avslått' . ($updatedByUser != null ? ' av ' . $updatedByUser->getDisplayName() : null); // TODO: Add localization for this.
					break;
			}
		}
	}

	/*
	 * Returns the content of this application.
	 */
	public function getContent(): string {
		return $this->content;
	}

	/*
	 * Returns the user that last updated this application.
	 */
	public function getUpdatedByUser(): ?User {
		return UserHandler::getUser($this->updatedByUserId);
	}

	/*
	 * Returns the comment of this application.
	 */
	public function getComment(): ?string {
		return $this->comment;
	}

	/*
	 * Accepts this application.
	 */
	public function accept(User $user, string $comment, bool $notify) {
		ApplicationHandler::acceptApplication($this, $user, $comment, $notify);
	}

	/*
	 * Rejects this application.
	 */
	public function reject(User $user, string $comment, bool $notify) {
		ApplicationHandler::rejectApplication($this, $user, $comment, $notify);
	}

	/*
	 * Closes this application.
	 */
	public function close(User $user) {
		ApplicationHandler::closeApplication($this, $user);
	}

	/*
	 * Add this application to the queue.
	 */
	public function queue(User $user, bool $notify) {
		ApplicationHandler::queueApplication($this, $user, $notify);
	}

	/*
	 * Remove this application from the queue.
	 */
	public function unqueue(User $user) {
		ApplicationHandler::unqueueApplication($this, $user);
	}

	/*
	 * Returns true if this application is in a queue, otherwise false.
	 */
	public function isQueued(): bool {
		return ApplicationHandler::isQueued($this);
	}
}
?>
