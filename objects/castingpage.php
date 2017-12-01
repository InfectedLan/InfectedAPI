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
require_once 'handlers/serverhandler.php';
require_once 'objects/object.php';

class CastingPage extends Object {
	private $eventId;
	private $name;
	private $data;
  private $template;

	/*
	 * Returns the id of the compo this server is bound to
	 */
  public function getEventId(): int {
    return $this->eventId;
	}

	/*
	 * Returns the compo object associated with this server
	 */
	public function getEvent(): Event {
		return EventHandler::getEvent($this->eventId);
	}

	/*
	 * Returns the human name of this server
	 */
	public function getName(): string {
		return $this->name;
	}

	/*
	 * Returns the connection data for this server. Note that the compo plugin is supposed to parse this however it wants. Might be json data. Might be a string. God knows.
	 */
	public function getData(): string {
		return $this->data;
	}

  /*
   * Returns the template associated with this page
   */
  public function getTemplate(): string {
    return $this->template;
  }

  /*
   * Sets the template associated with this page
   */
  public function setTemplate() {
		CastingPageHandler::setTemplate($this, $data);
  }

	/*
	 * Sets the connection details of this server
	 */
	public function setData($data) {
		CastingPageHandler::setData($this, $data);
	}

	/*
	 * Sets the human name of this server
	 */
	public function setHumanName($name) {
		CastingPageHandler::setName($this, $name);
	}

  /*
   * Deletes the server entry
   */
  public function delete() {
		CastingPageHandler::deleteServer($this);
  }
}
?>
