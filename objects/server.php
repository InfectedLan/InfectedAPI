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

require_once 'handlers/compohandler.php';
require_once 'handlers/serverhandler.php';
require_once 'objects/databaseobject.php';

class Server extends DatabaseObject {
	private $compoId;
	private $humanName;
	private $connectionData;

	/*
	 * Returns the id of the compo this server is bound to
	 */
  public function getCompoId(): int {
		return $this->compoId;
	}

	/*
	 * Returns the compo object associated with this server
	 */
	public function getCompo(): Compo {
		return CompoHandler::getCompo($this->compoId);
	}

	/*
	 * Returns the human name of this server
	 */
	public function getHumanName(): string {
		return $this->humanName;
	}

	/*
	 * Returns the connection data for this server. Note that the compo plugin is supposed to parse this however it wants. Might be json data. Might be a string. God knows.
	 */
	public function getConnectionData(): string {
		return $this->connectionData;
	}

	/*
	 * Sets the connection details of this server
	 */
	public function setConnectionDetails(string $details) {
		ServerHandler::setConnectionDetails($this, $details);
	}

	/*
	 * Sets the human name of this server
	 */
	public function setHumanName(string $humanName) {
		ServerHandler::setHumanName($this, $humanName);
	}

  /*
   * Deletes the server entry
   */
  public function delete() {
  	ServerHandler::deleteServer($this);
  }
}
?>
