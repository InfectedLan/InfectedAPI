<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2018 Infected <https://infected.no/>.
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
require_once 'objects/user.php';
require_once 'objects/event.php';
require_once 'handlers/nfcroompermissionhandler.php';
require_once 'settings.php';

/*
 * Represents a room in the building
 */
class Room extends DatabaseObject {
    //Constants for the type field

    private $name;
    private $timeLimited;

    /*
     * Returns the name of this room
     */
    public function getName(): String {
        return $this->name;
    }

    /*
     * Returns if this room is time limited(Mostly used for outside i suppose)
     */
    public function isTimeLimited(): bool {
        return $this->timeLimited==1;
    }

    /*
     * Returns if the given user has permissions to enter into this room
     */
    public function canEnter(User $user, Event $event = null): bool {
        return NfcRoomPermissionHandler::hasUserPermission($this, $user, $event);
    }

    /*
     * Returns the list of users assumed to be in this room
     */
    public function getUsersInRoom() {
        return RoomHandler::getUsersInRoom($this);
    }

}
?>