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

require_once 'handlers/seatmaphandler.php';
require_once 'handlers/rowhandler.php';
require_once 'objects/object.php';
require_once 'objects/row.php';

class Seatmap extends Object {
	private $humanName;
	private $backgroundImage;

	/*
	 * Returns the name of this seatmap.
	 */
	public function getHumanName() {
		return $this->humanName;
	}

	/*
	 * Returns the background image for this seatmap.
	 */
	public function getBackgroundImage() {
		return $this->backgroundImage;
	}

	/*
	 * Sets the background image for this seatmap.
	 */
	public function setBackgroundImage($filename) {
		SeatmapHandler::setBackground($this, $filename);
	}

	/*
	 * Returns the event this seatmap is accosiated with.
	 */
	public function getEvent() {
		return SeatmapHandler::getEvent($this);
	}

	/*
	 * Returns the rows for this seatmap.
	 */
	public function getRows() {
		return RowHandler::getRowsBySeatmap($this);
	}

	/*
	 * Add an row to this seatmap at the specified coordinates.
	 */
	public function addRow($x, $y) {
		return RowHandler::createRow($this, $x, $y);
	}

	/*
	 * Remove an row to this seatmap.
	 */
	public function removeRow(Row $row) {
		return RowHandler::removeRow($row);
	}
}
?>
