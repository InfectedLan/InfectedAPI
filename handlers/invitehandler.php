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
require_once 'objects/invite.php';
require_once 'objects/user.php';
require_once 'objects/clan.php';

class InviteHandler {
	/*
	 * Get a invite by the internal id.
	 */
	public static function getInvite($id) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '`
																WHERE `id` = \'' . $id . '\';');

		return $result->fetch_object('Invite');
	}

	/*
	 * Get all invites.
	 */
	public static function getInvites() {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `'  . Settings::db_table_infected_compo_invites . '`;');

		$inviteList = [];

		while ($object = $result->fetch_object('Invite')) {
			$inviteList[] = $object;
		}

		return $inviteList;
	}

	/*
	 * Get all invites for the specified user.
	 */
	public static function getInvitesByUser(User $user) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `'  . Settings::db_table_infected_compo_invites . '`
																WHERE `userId` = \'' . $user->getId() . '\';');

		$inviteList = [];

		while ($object = $result->fetch_object('Invite')) {
			$inviteList[] = $object;
		}

		return $inviteList;
	}

	/*
	 * Get all invites for a clan.
	 */
	public static function getInvitesByClan(Clan $clan) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_compo_invites . '`
																WHERE `clanId` = \'' . $clan->getId() . '\';');

		$inviteList = [];

		while ($object = $result->fetch_object('Invite')) {
			$inviteList[] = $object;
		}

		return $inviteList;
	}

	/*
	 * Invite the specified user to the specifed clan.
	 */
	public static function createInvite(Clan $clan, User $user, Event $event = null) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_invites . '` (`eventId`, `userId`, `clanId`)
										  VALUES (\'' . ($event != null ? $event->getId() : EventHandler::getCurrentEvent()->getId()) . '\',
														  \'' . $user->getId() . '\',
														  \'' . $clan->getId() . '\');');

		$invite = self::getInvite($database->insert_id);


		return $invite;
	}

	/*
	 * Accept the specified invite.
	 */
	public static function acceptInvite(Invite $invite) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_invites . '`
						  				WHERE `id` = \'' . $invite->getId() . '\';');

		$clan = $invite->getClan();
		$memberList = ClanHandler::getPlayingClanMembers($clan);
    $compo = $clan->getCompo();
		$stepInId = count($memberList) < $compo->getTeamSize() ? 0 : 1;

		$database->query('INSERT INTO `' . Settings::db_table_infected_compo_memberof . '` (`userId`, `clanId`, `stepInId`)
											VALUES (\'' . $invite->getUser()->getId() . '\',
															\'' . $clan->getId() . '\',
															\'' . $stepInId . '\');');

		if (count($memberList) == $compo->getTeamSize()-1) {
	    //NEW: steamid check
	    $canQualify = !$compo->requiresSteamId();

			if (!$canQualify) {
				//Compo requires steam id
				foreach ($memberList as $member) {
			    if ($member->getSteamId() === null) {
						$canQualify = false;
						break;
			    } else {
						$canQualify = true;
			    }
				}

			$canQualify = $canQualify && $invite->getUser()->getSteamId() !== null;
		}

    if ($canQualify) {
			$playingClans = ClanHandler::getQualifiedClansByCompo($compo);

			if (count($playingClans) < $compo->getParticipantLimit() || $compo->getParticipantLimit() == 0) {
				ClanHandler::setQualified($clan, true);
			} else if (!ClanHandler::isInQualificationQueue($clan)) {
			  ClanHandler::addToQualificationQueue($clan);
			}
    }
		}
	}

	/*
	 * Decline the specified invite.
	 */
	public static function declineInvite(Invite $invite) {
		$database = Database::getConnection(Settings::db_name_infected_compo);

		$database->query('DELETE FROM `' . Settings::db_table_infected_compo_invites . '`
											WHERE `id` = \'' . $invite->getId() . '\';');
	}
}
?>
