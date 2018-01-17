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

require_once 'database.php';
require_once 'settings.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/invitehandler.php';
require_once 'handlers/userhandler.php';
require_once 'objects/eventobject.php';

class Invite extends EventObject {
	private $userId;
	private $clanId;

	/*
	 * Returns the user that this invite is for.
	 */
	public function getUser(): User {
		return UserHandler::getUser($this->userId);
	}

	/*
	 * Returns the clan this invite is to.
	 */
	public function getClan(): Clan {
		return ClanHandler::getClan($this->clanId);
	}

	/*
	 * Accept this invite.
	 */
	public function accept() {
		InviteHandler::acceptInvite($this);
	}

	/*
	 * Decline this invite.
	 */
	public function decline() {
		InviteHandler::declineInvite($this);
	}
}