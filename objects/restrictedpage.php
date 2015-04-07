<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'handlers/grouphandler.php';
require_once 'handlers/teamhandler.php';
require_once 'page.php';

class RestrictedPage extends Page {
	private $groupId;
	private $teamId;
	
	/*
	 * Returns the group of this page.
	 */
	public function getGroup() {
		return GroupHandler::getGroup($this->groupId);
	}
	
	/*
	 * Returns the team of this page.
	 */
	public function getTeam() {
		return TeamHandler::getTeam($this->teamId);
	}
}
?>