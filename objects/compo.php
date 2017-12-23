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

require_once 'handlers/matchhandler.php';
require_once 'objects/eventobject.php';

class Compo extends EventObject {
  const CONNECTION_TYPE_NONE = 0;
  const CONNECTION_TYPE_SERVER = 1;
  const CONNECTION_TYPE_CUSTOM = 2;

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
  private $connectionType;
  private $requiresSteamId;

  /*
   * Returns the name of this compo.
   */
  public function getName(): string {
    return $this->name;
  }

  /*
   * Returns the title of this compo.
   */
  public function getTitle(): string {
    return $this->title;
  }

  /*
   * Returns the tag of this compo.
   */
  public function getTag(): string {
    return $this->tag;
  }

  /*
   * Returns the description of this compo.
   */
  public function getDescription(): string {
    return $this->description;
  }

  /*
   * Returns the gamemode for this compo. Note we are not returning the object, as this is done on request depending on if it is JS or php we want.
   */
  public function getPluginName(): string {
    return $this->pluginName;
  }

  /*
   * Returns the startTime of this compo.
   */
  public function getStartTime(): int {
    return strtotime($this->startTime);
  }

  /*
   * Returns the registration deadline of this compo.
   */
  public function getRegistrationEndTime(): int {
    return strtotime($this->registrationEndTime);
  }

  /*
   * Returns the size of this team.
   */
  public function getTeamSize(): int {
    return $this->teamSize;
  }

  /*
   * Returns the chat used by this compo
   */
  public function getChat(): Chat {
    return ChatHandler::getChat($this->chatId);
  }

  /*
   * Returns the chat id used by this compo
   */
  public function getChatId(): int {
    return $this->chatId;
  }

  public function getParticipantLimit(): int {
    return $this->participantLimit;
  }

  /*
   * Return a list of all matches for this compo.
   */
  public function getMatches(): array {
    return MatchHandler::getMatchesByCompo($this);
  }


  public function getConnectionType(): int {
    return $this->connectionType;
  }

  /*
   * Returns true if the compo requires steam id to qualify
   */
  public function requiresSteamId(): bool {
    return $this->requiresSteamId == "1" ? true : false;
  }
}
?>
