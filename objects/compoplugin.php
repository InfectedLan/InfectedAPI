<?php
/**
  * This file is part of InfectedCrew.
  *
  * Copyright (C) 2017 Infected <http://infected.no/>.
  *
  * This program is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 3 of the License, or
  * (at your option) any later version.
  *
  * This program is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU General Public License for more details.
  * You should have received a copy of the GNU General Public License
  * along with this program. If not, see <http://www.gnu.org/licenses/>.
  */

require_once 'handlers/matchhandler.php';

class CompoPlugin {
    /*
     * Called for each current match on the crew page
     */
    public function getCustomMatchInformation(Match $match) {
        return null;
    }

    public function hasVoteScreen(): bool {
        return false;
    }

    public function getTurnArray(Match $match): array {
        return [0, 1, 0, 1, 1, 0, 2];
    }

    public function getTurnMask(Match $match): array {
        return [0, 0, 0, 0, 0, 0, 2];
    }

    public function onMatchFinished(Match $match) {

    }
}