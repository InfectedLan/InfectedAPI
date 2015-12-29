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
require_once 'server.php';

class WebSocketPlugin {
    function __construct(Server $server) {
        //Register intents here
    }

    public function handleIntent($intent, $args, WebSocketUser $connection) {
        
    }

    public function onConnect(WebSocketUser $connection) {

    }

    public function onDisconnect(WebSocketUser $connection) {

    }

    public function tick() {
        //Please note that this is not strictly well-timed, but ticks about every second. "around" is a nice word in programming. It means "prepare for unexpected shit"
    }
}
?>