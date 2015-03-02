/*
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

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

<?php
require_once 'objects/object.php';

class Game extends Object {
	private $name;
	private $title;
	private $price;
	private $mode;
	private $description;
	private $startTime;
	private $endTime;
	private $published;
	
	/*
	 * Returns the name of this game.
	 */ 
	public function getName() {
		return $this->name;
	}
	
	/*
	 * Returns the title of this game.
	 */ 
	public function getTitle() {
		return $this->title;
	}
	
	/*
	 * Returns the price for game.
	 */ 
	public function getPrice() {
		return $this->price;
	}
	
	/*
	 * Returns the mode of this game.
	 */ 
	public function getMode() {
		return $this->mode;
	}

	/*
	 * Returns the description of this game.
	 */ 
	public function getDescription() {
		return $this->description;
	}
	
	/*
	 * Returns the startTime of this game.
	 */ 
	public function getStartTime() {
		return strtotime($this->startTime);
	}
	
	/*
	 * Returns the endTime of this game.
	 */ 
	public function getEndTime() {
		return strtotime($this->endTime);
	}
	
	/*
	 * Returns the bookingTime of this game.
	 */ 
	public function isBookingTime() {	
		return time() >= $this->getStartTime() && time() <= $this->getEndTime();
	}
	
	/*
	 * Returns true if this game is published.
	 */ 
	public function isPublished() {
		return $this->published ? true : false;
	}	
}
?>