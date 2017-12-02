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
require_once 'handlers/agendahandler.php';
require_once 'utils/dateutils.php';

$agendaList = [];

foreach (AgendaHandler::getPublishedNotHappendAgendas() as $agenda) {
	$agendaList[] = ['id' => $agenda->getId(),
								   'name' => $agenda->getName(),
								   'title' => $agenda->getTitle(),
								   'description' => $agenda->getDescription(),
								   'startTime' => DateUtils::getDayFromInt(date('w', $agenda->getStartTime())) . ' ' . date('H:i', $agenda->getStartTime()),
								   'isHappening' => $agenda->isHappening()];
}

header('Content-Type: text/plain');
echo json_encode(array('agendaList' => $agendaList), JSON_PRETTY_PRINT);
Database::cleanup();
?>
