<?php
require_once 'handlers/slidehandler.php';
	
$slideList = array();

foreach (SlideHandler::getPublishedSlides() as $slide) {
	array_push($slides, array('id' => $slide->getId(),
							  'name' => $slide->getName(),
							  'title' => $slide->getTitle(),
							  'content' => $slide->getContent(),
							  'startTime' => $slide->getStartTime(),
							  'endTime' => $slide->getEndTime(),
							  'isPublished' => $slide->isPublished()));
}

echo json_encode(array('slideList' => $slideList));
?>