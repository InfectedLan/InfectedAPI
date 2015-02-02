<?php
require_once 'handlers/slidehandler.php';

$slideList = SlideHandler::getPublishedSlides();	
$slides = array();

foreach ($slideList as $slide) {
	array_push($slides, array('id' => $slide->getId(),
							  'name' => $slide->getName(),
							  'title' => $slide->getTitle(),
							  'content' => $slide->getContent(),
							  'startTime' => $slide->getStartTime(),
							  'endTime' => $slide->getEndTime(),
							  'isPublished' => $slide->isPublished()));
}

echo json_encode(array('slides' => $slides));
?>