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

require_once 'settings.php';
require_once 'database.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/invitehandler.php';
require_once 'handlers/userhandler.php';
require_once 'objects/eventobject.php';
require_once 'objects/user.php';

class Clan extends EventObject {
	private $name;
	private $tag;
	private $chiefId;

	/*
	 * Return the name of this clan.
	 */
	public function getName() {
		return $this->name;
	}

	/*
	 * Return the tag of this clan.
	 */
	public function getTag() {
		return $this->tag;
	}

	/*
	 * Return the chief of this clan.
	 */
	public function getChief() {
		return UserHandler::getUser($this->chiefId);
	}

  /*
   * Return the user id of the chief of this clan
   */
  public function getChiefId() {
  	return $this->chiefId;
  }

	/*
	 * Return the compo of this clan.
	 */
	public function getCompo() {
		return CompoHandler::getCompoByClan($this);
	}

	/*
	 * Returns a list of all the clan members.
	 */
	public function getMembers() {
		return ClanHandler::getClanMembers($this);
	}

	/*
	 * Returns a list of all playing members of this clan.
	 */
	public function getPlayingMembers() {
		return ClanHandler::getPlayingClanMembers($this);
	}

	/*
	 * Returns a list of all step in members of this clan.
	 */
	public function getStepInMembers() {
		return ClanHandler::getStepInClanMembers($this);
	}

	/*
	 * Returns a list of all invites to this clan.
	 */
	public function getInvites() {
		return ClanHandler::getInvitesByClan($this);
	}

	/*
	 * Set the step in state of a member.
	 */
	public function setStepInMemberState(User $user, $state) {
 		ClanHandler::SetStepInClanMemberState($this, $user, $state);
	}

	/*
	 * Returns true if this clan is qualified for specified compo.
	 */
	public function isQualified($compo) {
    if (!ClanHandler::isQualified($this, $compo)) {
    	return false;
    }

		// TODO: Move this database stuff, should really be in a handler.
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantof . '`
																WHERE `clanId` = \'' . $this->getId() . '\'
																AND `compoId` = \'' . $database->real_escape_string($compo->getId()) . '\';');

		return $result->num_rows > 0;
	}

	/*
	 * Returns true of the specified user is member of this clan.
	 */
	public function isMember(User $user) {
		return ClanHandler::isClanMember($this, $user);
	}

	/*
	 * Return true if the specified user is a stepin member of this clan.
	 */
	public function isStepInMember(User $user) {
		return ClanHandler::isStepInClanMember($this, $user);
	}

	/*
	 * Invite the specified user to this clan.
	 */
	public function invite(User $user) {
		InviteHandler::createInvite($this, $user);
	}

	/*
	 * Kick the specified user from this clan.
	 */
	public function kick(User $user) {
		ClanHandler::kickFromClan($this, $user);
	}
}
?>
