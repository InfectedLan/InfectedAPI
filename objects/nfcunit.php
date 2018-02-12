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
require_once 'handlers/eventhandler.php';
require_once 'handlers/roomhandler.php';
require_once 'objects/room.php';

/*
 * Represents a unit that offers some kind of interface between an NFC card and the infected.no API, for example:
 *  - Ticket booth
 *  - Entry gate
 */
class NfcUnit extends DatabaseObject {
	//Constants for the type field
	const NFC_GATE_TYPE_POS = 2;
	const NFC_GATE_TYPE_TICKETSCANNER = 1;
    const NFC_GATE_TYPE_GATE = 0;

	private $eventId;
	private $pcbId;
	private $name;
	private $type;
	private $fromRoom;
	private $toRoom;
	
	/*
	 * Returns the event this card entry is assigned to
	 */
	public function getEvent() {
		return EventHandler::getEvent($this->eventId);
	}

	/*
	 * Returns the pcb id(unique id) for this gate
	 */
	public function getPcbId() {
		return $this->pcbId;
	}
	
	/*
	 * Returns the name of the entrance.
	 * Note that this should be something like "Ticket reader" if only used to scan tickets, or a location description if entrance gate
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Returns the type of NFC gate. See the constants above.
	 */
	public function getType() {
		return $this->type;
	}

    /*
     * Returns the room this unit is in. Should only be used by gates.
     * If it is called on an unit which is not a gate, it will return null, throwing an exception.
    */
    public function getFromRoom() : Room {
        return $this->type != self::NFC_GATE_TYPE_GATE ? null : RoomHandler::getRoom($this->fromRoom);
    }
    /*
     * Returns the room this unit goes into. Should only be used by gates.
     * If it is called on an unit which is not a gate, it will return null, throwing an exception.
     */
    public function getToRoom() : Room {
        return $this->type != self::NFC_GATE_TYPE_GATE ? null : RoomHandler::getRoom($this->toRoom);
    }
}
?>