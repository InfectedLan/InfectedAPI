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

require_once 'objects/eventobject.php';

class Agenda extends EventObject {
	private $name;
	private $title;
	private $description;
	private $startTime;
	private $published;
	
	/*
	 * Returns the name of this object.
	 */
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the name of this object.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/*
	 * Returns the description for this agenda.
	 */
	public function getDescription() {
		return $this->description;
	}
	
	/*
	 * Returns the startTime of this agenda.
	 */
	public function getStartTime() {
		return strtotime($this->startTime);
	}
	
	/*
	 * Returns true if this agenda is published.
	 */
	public function isPublished() {
		return $this->published ? true : false;
	}
	
	/*
	 * Returns true if this agenda is happening right now.
	 */
	public function isHappening() {
		return $this->getStartTime() - 5 * 60 >= time() || 
			   $this->getStartTime() + 1 * 60 * 60 >= time();
	}
}
?>