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

require_once 'handlers/compohandler.php';
require_once 'objects/object.php';

class VoteOption extends Object{
	private $compoId;
	private $thumbnailUrl;
	private $name;

	/*
	 * Returns the compo of this vote option.
	 */
	public function getCompo() {
		return CompoHandler::getCompo($this->compoId);
	}

	/*
	 * Return the thumbnail url for this vote option.
	 */
	public function getThumbnailUrl() {
		return $this->thumbnailUrl;
	}

	/*
	 * Returns the name of this vote option.
	 */
	public function getName() {
		return $this->name;
	}
}
?>