<?php
/**
 * This file is part of InfectedCrew.
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

require_once 'session.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/eventhandler.php';
require_once 'handlers/castingpagehandler.php';
require_once 'objects/compo.php';

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();

	if ($user->hasPermission('compo.casting')) {
    $castingPage = CastingPageHandler::getCastingPage($_GET['id']);

		if ($castingPage != null) {
        echo '<html>';
        echo '<head>';
        echo '<title>Infected CASTING page system </title>';
        echo '<script src="../scripts/jquery-1.11.3.min.js"></script>';
        echo '<script src="../scripts/casting.js"></script>';
        echo '<link rel="stylesheet" href="../styles/cast.css">';
        echo '</head>';
        echo '<body>';
  			echo '<div class="fullscreen-bg"><video width="100%" height="100%" autoplay loop><source src="../content/static/casting_vid.mp4" type="video/mp4" /></video></div>';
        echo '<script>var castingPageData = ' . $castingPage->getData() . '; var template = "' . $castingPage->getTemplate() . '";$(document).ready(function(){renderCasting();});</script>';
        echo '<div id="content"></div>';
        echo '</body>';
        echo '</html>';
    } else {
      echo '<p>Casting-siden finnes ikke</p>';
    }
	} else {
    echo '<p>Du har ikke rettigheter til dette!</p>';
	}
} else {
	echo '<p>Du er ikke logget inn!</p>';
}
?>
