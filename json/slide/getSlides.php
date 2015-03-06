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

require_once 'handlers/slidehandler.php';

$slideList = array();

foreach (SlideHandler::getSlides() as $slide) {
	array_push($slides, array('id' => $slide->getId(),
							  'name' => $slide->getName(),
							  'title' => $slide->getTitle(),
							  'content' => $slide->getContent(),
							  'startTime' => $slide->getStartTime(),
							  'endTime' => $slide->getEndTime(),
							  'isPublished' => $slide->isPublished()));
}

header('Content-Type: text/plain');
echo json_encode(array('slideList' => $slideList), JSON_PRETTY_PRINT);
?>