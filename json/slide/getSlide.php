<?php
require_once 'handlers/slidehandler.php';

$slideList = SlideHandler::getSlides();	
$slides = array();

foreach ($slideList as $slide) {
	array_push($slides, array('id' => $slide->getId(),
							  'start' => $slide->getStart(),
							  'end' => $slide->getEnd(),
							  'title' => $slide->getTitle(),
							  'content' => $slide->getContent(),
							  'isPublished' => $slide->isPublished()));
}

echo json_encode(array('slides' => $slides));
?>