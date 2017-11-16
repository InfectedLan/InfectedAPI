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

require_once 'objects/object.php';
require_once 'handlers/nfcgatehandler.php';
require_once 'handlers/nfccardhandler.php';

/*
 * Tracks person movements through gates with type 0
 */
class NfcLogEntry extends Object {
	//Constants for the type field

	private $timestamp;
	private $gateId;
	private $nfcId;
	
	/*
	 * Returns the time of this event
	 */
	public function getTime() {
		return strtotime($this->timestamp);
	}

	/*
	 * Returns the gate which this entry logs passing
	 */
	public function getGate() {
		return NfcGateHandler::getGate($this->gateId);
	}
	
	/*
	 * Returns the nfc card that this entry is about
	 */
	public function getCard() {
		return NfcCardHandler::getCard($this->nfcId);
	}

	/*
	 * Returns the type of NFC gate. See the constants above.
	 */
	public function getType() {
		return $this->type;
	}


}
?>