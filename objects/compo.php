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

require_once 'handlers/matchhandler.php';
require_once 'objects/eventobject.php';

class Compo extends EventObject {
	private $name;
	private $title;
	private $tag;
	private $description;
	private $mode;
	private $price;
	private $startTime;
	private $registrationDeadline;
	private $teamSize;

	/*
	 * Returns the name of this compo.
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Returns the title of this compo.
	 */
	public function getTitle() {
		return $this->title;
	}

	/*
	 * Returns the tag of this compo.
	 */
	public function getTag() {
		return $this->tag;
	}

	/*
	 * Returns the description of this compo.
	 */
	public function getDescription() {
		return $this->description;
	}

	/*
	 * Returns the gamemode for this compo.
	 */
	public function getMode() {
		return $this->mode;
	}

	/*
	 * Returns the price of this compo.
	 */
	public function getPrice() {
		return (int) $this->price;
	}

	/*
	 * Returns the startTime of this compo.
	 */
	public function getStartTime() {
		return strtotime($this->startTime);
	}

	/*
	 * Returns the registration deadline of this compo.
	 */
	public function getRegistrationDeadline() {
		return strtotime($this->registrationDeadline);
	}

	/*
	 * Returns the size of this team.
	 */
	public function getTeamSize() {
		return (int) $this->teamSize;
	}

	/*
	 * Return a list of all matches for this compo.
	 */
	public function getMatches() {
		return MatchHandler::getMatchesByCompo($this);
	}
}
?>