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

require_once 'handlers/matchhandler.php';
require_once 'objects/eventobject.php';

class Compo extends EventObject {
    private $name;
    private $title;
    private $tag;
    private $description;
    private $pluginName;
    private $startTime;
    private $registrationEndTime;
    private $teamSize;
    private $participantLimit;
    private $chatId;

    /*
     * Returns the name of this compo.
     */
    public function getName() {
	return $this->name;
    }

    /*
     * Returns the title of this compo.
     */
    public function getTitle() {
	return $this->title;
    }

    /*
     * Returns the tag of this compo.
     */
    public function getTag() {
	return $this->tag;
    }

    /*
     * Returns the description of this compo.
     */
    public function getDescription() {
	return $this->description;
    }

    /*
     * Returns the gamemode for this compo. Note we are not returning the object, as this is done on request depending on if it is JS or php we want.
     */
    public function getPluginName() {
	return $this->pluginName;
    }

    /*
     * Returns the startTime of this compo.
     */
    public function getStartTime() {
	return strtotime($this->startTime);
    }

    /*
     * Returns the registration deadline of this compo.
     */
    public function getRegistrationEndTime() {
	return strtotime($this->registrationEndTime);
    }

    // TODO: Remove this, keeping for now for compatibility reasons.
    public function getRegistrationDeadline() {
	return $this->getRegistrationEndTime();
    }

    /*
     * Returns the size of this team.
     */
    public function getTeamSize() {
	return $this->teamSize;
    }

    /*
     * Returns the chat used by this compo
     */
    public function getChat() {
	return ChatHandler::getChat($this->chatId);
    }

    /*
     * Returns the chat id used by this compo
     */
    public function getChatId() {
	return $this->chatId;
    }

    public function getParticipantLimit() {
        return $this->participantLimit;
    }

    /*
     * Return a list of all matches for this compo.
     */
    public function getMatches() {
	return MatchHandler::getMatchesByCompo($this);
    }
}
?>
