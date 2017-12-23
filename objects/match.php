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
require_once 'handlers/chathandler.php';
require_once 'handlers/clanhandler.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/matchhandler.php';
require_once 'handlers/userhandler.php';
require_once 'objects/databaseobject.php';

class Match extends DatabaseObject {
  const STATE_READYCHECK = 0;
  const STATE_CUSTOM_PREGAME = 1;
  const STATE_JOIN_GAME = 2;

  const BRACKET_WINNER = 1;
  const BRACKET_LOOSER = 0;

  private $scheduledTime;
  private $connectDetails;
  private $winnerId;
  private $state;
  private $compoId;
  private $bracketOffset;
  private $chatId;
  private $bracket;

  public function getBracket() {
    return $this->bracket;
  }

  public function getChat(): Chat {
    return ChatHandler::getChat($this->chatId);
  }

  public function getChatId(): int {
    return $this->chatId;
  }

  public function getScheduledTime(): int {
    return strtotime($this->scheduledTime);
  }

  public function getConnectDetails(): string {
    return $this->connectDetails;
  }

  public function getWinner(): Clan {
    return ClanHandler::getClan($this->winnerId);
  }

  public function getWinnerId(): int {
    return $this->winnerId;
  }

  public function getState(): int {
    return $this->state;
  }

  public function getBracketOffset(): int {
    return $this->bracketOffset;
  }

  public function isParticipant($user): bool {
    foreach (MatchHandler::getParticipantsByMatch($this) as $clan) {
      if ($clan->isMember($user)) {
  	     return true;
      }
    }

    return false;
  }

  /*
   * Returns true if the match can be run
   */
  public function isReady(): bool {
    return MatchHandler::isReady($this);
  }

  public function setState($state) {
    $this->state = $state;

    MatchHandler::updateMatch($this, $state);
  }

  public function getCompo(): Compo {
    return CompoHandler::getCompo($this->compoId);
  }

  public function getParents() : array {
    return MatchHandler::getMatchParents($this);
  }

  public function getJsonableData(): array {
    return MatchHandler::getJsonableData($this);
  }
}
?>
