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
require_once 'objects/eventobject.php';

class RestrictedPage extends EventObject {
	private $name;
	private $title;
	private $content;
	private $groupId;
	private $teamId;

	/*
	 * Returns the name of this page.
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Returns the title of this page.
	 */
	public function getTitle() {
		return $this->title;
	}

	/*
	 * Returns the content of this page.
	 */
	public function getContent() {
		return $this->content;
	}

	/*
	 * Returns the true if the page is global in regards to group.
	 */
	public function isGroupGlobal() {
		return $this->groupId == 0;
	}

	/*
	 * Returns the group of this page.
	 */
	public function getGroup() {
		return GroupHandler::getGroup($this->groupId);
	}

	/*
	 * Returns the true if the page is global in regards to team.
	 */
	public function isTeamGlobal() {
		return $this->teamId == 0;
	}

	/*
	 * Returns the team of this page.
	 */
	public function getTeam() {
		return TeamHandler::getTeam($this->teamId);
	}
}
?>
