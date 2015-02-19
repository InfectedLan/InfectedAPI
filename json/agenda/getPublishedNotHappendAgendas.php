<?php
require_once 'handlers/agendahandler.php';
require_once 'utils/dateutils.php';

$agendaList = array();

foreach (AgendaHandler::getPublishedNotHappendAgendas() as $agenda) {
	array_push($agendaList, array('id' => $agenda->getId(),
									 'name' => $agenda->getName(),
									 'title' => $agenda->getTitle(),
									 'description' => $agenda->getDescription(),
									 'startTime' => DateUtils::getDayFromInt(date('w', $agenda->getStartTime())) . ' ' . date('H:i', $agenda->getStartTime()),
									 'isHappening' => $agenda->isHappening()));
}

echo json_encode(array('agendaList' => $agendaList));
?>