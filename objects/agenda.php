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

require_once 'objects/eventobject.php';

class Agenda extends EventObject {
	private $name;
	private $title;
	private $description;
	private $secondsOffset;
	private $published;

	/*
	 * Returns the name of this object.
	 */
	public function getName(): string {
		return $this->name;
	}

	/*
	 * Returns the name of this object.
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/*
	 * Returns the description for this agenda.
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/*
	 * Returns the seconds offset of this agenda.
	 */
	public function getSecondsOffset(): int {
		return $this->secondsOffset;
	}

	/*
	 * Returns the startTime of this agenda.
	 */
	public function getStartTime(): int {
		$event = EventHandler::getCurrentEvent();
		$eventDate = strtotime(date('Y-m-d', $event->getStartTime()));

		return $eventDate + $this->getSecondsOffset();
	}

	/*
	 * Returns true if this agenda is published.
	 */
	public function isPublished(): bool {
		return $this->published ? true : false;
	}

	/*
	 * Returns true if this agenda is happening right now.
	 */
	public function isHappening(): bool {
		return $this->getStartTime() - 5 * 60 >= time() || $this->getStartTime() + 1 * 60 * 60 >= time();
	}
}
?>