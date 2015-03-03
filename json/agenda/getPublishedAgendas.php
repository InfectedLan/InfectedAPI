<?php
/**
 * This file is part of InfectedAPI.
 *
 * Copyright (C) 2015 Infected <http://infected.no/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'handlers/agendahandler.php';
require_once 'utils/dateutils.php';

$agendaList = array();

foreach (AgendaHandler::getPublishedAgendas() as $agenda) {
	array_push($agendaList, array('id' => $agenda->getId(),
								  'name' => $agenda->getName(),
								  'title' => $agenda->getTitle(),
							 	  'description' => $agenda->getDescription(),
								  'startTime' => DateUtils::getDayFromInt(date('w', $agenda->getStartTime())) . ' ' . date('H:i', $agenda->getStartTime()),
								  'isHappening' => $agenda->isHappening()));
}

echo json_encode(array('agendaList' => $agendaList));
?>