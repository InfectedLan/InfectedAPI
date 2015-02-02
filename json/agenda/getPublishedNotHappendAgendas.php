<?php
require_once 'utils.php';
require_once 'handlers/agendahandler.php';

$agendaList = AgendaHandler::getPublishedNotHappendAgendas();	
$newAgendaList = array();

foreach ($agendaList as $agenda) {
	array_push($newAgendaList, array('id' => $agenda->getId(),
									 'name' => $agenda->getName(),
									 'title' => $agenda->getTitle(),
									 'description' => $agenda->getDescription(),
									 'startTime' => Utils::getDayFromInt(date('w', $agenda->getStartTime())) . ' ' . date('H:i', $agenda->getStartTime()),
									 'isHappening' => $agenda->isHappening()));
}

echo json_encode(array('agendaList' => $newAgendaList));
?>