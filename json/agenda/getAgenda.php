<?php
require_once 'utils.php';
require_once 'handlers/agendahandler.php';

$agendaList = AgendaHandler::getAgendas();	
$newAgendaList = array();

foreach ($agendaList as $agenda) {
	array_push($newAgendaList, array('id' => $agenda->getId(),
								'event' => $agenda->getEvent()->getId(),
								'name' => $agenda->getName(),
								'title' => $agenda->getTitle(),
								'description' => $agenda->getDescription(),
								'start' => Utils::getDayFromInt(date('w', $agenda->getStart())) . ' ' . date('H:i', $agenda->getStart()),
								'isHappening' => $agenda->isHappening()));
}

echo json_encode(array('agendaList' => $newAgendaList));
?>