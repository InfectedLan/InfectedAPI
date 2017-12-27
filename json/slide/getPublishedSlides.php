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
require_once 'localization.php';
require_once 'handlers/slidehandler.php';

$slideList = [];

foreach (SlideHandler::getPublishedSlides() as $slide) {
	$slideList[] = ['id' => $slide->getId(),
							    'name' => $slide->getName(),
						  	  'title' => $slide->getTitle(),
					  		  'content' => $slide->getContent(),
							    'startTime' => $slide->getStartTime(),
							    'endTime' => $slide->getEndTime(),
							    'isPublished' => $slide->isPublished()];
}

header('Content-Type: application/json');
echo json_encode(['slideList' => $slideList], JSON_PRETTY_PRINT);
Database::cleanup();
?>
