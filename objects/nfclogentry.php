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

require_once 'objects/databaseobject.php';
require_once 'objects/nfccard.php';
require_once 'objects/nfcunit.php';
require_once 'handlers/nfcunithandler.php';
require_once 'handlers/nfccardhandler.php';

/*
 * Tracks person movements through gates with type 0
 */
class NfcLogEntry extends DatabaseObject {
	//Constants for the type field

	private $timestamp;
	private $gateId;
	private $cardId;
	private $legalPass;
	
	/*
	 * Returns the time of this event
	 */
	public function getTime() : int{
		return strtotime($this->timestamp);
	}

	/*
	 * Returns the gate which this entry logs passing
	 */
	public function getGate() : NfcUnit{
		return NfcUnitHandler::getGate($this->gateId);
	}
	
	/*
	 * Returns the nfc card that this entry is about
	 */
	public function getCard() : NfcCard{
		return NfcCardHandler::getCard($this->cardId);
	}

	/*
	 * Returns the type of NFC gate. See the constants above.
	 */
	public function getType() : int {
		return $this->type;
	}

	/*
	 * Returns if the pass was legal or not
	 */
	public function isLegalPass() : bool {
	    return $this->legalPass == 1;
    }

}
?>