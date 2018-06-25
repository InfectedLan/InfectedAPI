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

require_once 'handlers/nfcroompermissionhandler.php';
require_once 'objects/databaseobject.php';
require_once 'settings.php';

/*
 * Tracks permissions for nfc gates
 */
class NfcRoomPermission extends DatabaseObject {
    //Constants for the type field

    private $roomId;
    private $permissionType;
    private $permissionArg;

    //Entitlement types
    const PERMISSION_TYPE_USER = 0;
    const PERMISSION_TYPE_CREW = 1;

    public function getRoom() : Room {
        return RoomHandler::getRoom($this->roomId);
    }

    /*
     * Returns the type of permission rule this entry represents
     */
    public function getPermissionType(): int {
        return $this->permissionType;
    }

    /*
     * Returns the argument for this permission
     */
    public function getPermissionArg(): int {
        return $this->permissionArg;
    }

}
?>