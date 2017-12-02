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

require_once 'handlers/eventhandler.php';
require_once 'objects/databaseobject.php';

class Slide extends EventObject {
	private $name;
	private $title;
	private $content;
	private $startTime;
	private $endTime;
	private $published;

	/*
	 * Returns the name of this slide.
	 */
	public function getName(): string {
		return $this->name;
	}

	/*
	 * Returns the title of this slide.
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/*
	 * Returns the content of this slide.
	 */
	public function getContent(): string {
		return $this->content;
	}

	/*
	 * Returns the start time of this slide.
	 */
	public function getStartTime(): int {
		return strtotime($this->startTime);
	}

	/*
	 * Returns the end time of this slide.
	 */
	public function getEndTime(): int {
		return strtotime($this->endTime);
	}

	/*
	 * Returns true if this slide is published.
	 */
	public function isPublished(): bool {
		return $this->published ? true : false;
	}
}
?>
