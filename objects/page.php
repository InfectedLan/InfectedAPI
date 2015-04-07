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

require_once 'objects/object.php';

class Page extends Object {
	protected $name;
	protected $title;
	protected $content;
	
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
}
?>