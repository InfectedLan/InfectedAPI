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

require_once 'session.php';
require_once 'localization.php';
require_once 'handlers/seatmaphandler.php';

const img_width = 27+4;
const img_height = 27+4;
const img_margin = 1;

if (Session::isAuthenticated()) {
  $user = Session::getCurrentUser();

  if (isset($_GET['id'])) {
  	$seatmap = SeatmapHandler::getSeatmap($_GET['id']);

  	if ($seatmap != null) {
	    //echo "hi";
	    $image = imagecreatefrompng(Settings::getValue("api_path") . 'content/seatmapBackground/' . $seatmap->getBackgroundImage());

      foreach ($seatmap->getRows() as $row) {
    		doRow($row, $image);
	    }

      /*
      $seatData = [];

      foreach ($row->getSeats() as $seat) {
      $data = [];

      $data['id'] = $seat->getId();
      $data['number'] = $seat->getNumber();
      $data['humanName'] = $seat->getString();

      if ($seat->hasTicket()) {
      $ticket = $seat->getTicket();

      $data['occupied'] = true;
      $data['occupiedTicket'] = ['id' => $ticket->getId(),
      'owner' => htmlspecialchars($ticket->getUser()->getDisplayName())];
      } else {
      $data['occupied'] = false;
      }

      $seatData[] = $data;
      }

      $seatmapData[] = ['seats' => $seatData,
      'id' => $row->getId(),
      'x' => $row->getX(),
      'y' => $row->getY(),
      'number' => $row->getNumber()];

      $result = true;
      }
      */

      header('Content-Type: image/png');
      imagepng($image);
  	} else {
  	  echo '<h1>' . Localization::getLocale('this_seatmap_does_not_exist') . '</h1>';
  	}
  } else {
    echo '<h1>' . Localization::getLocale('no_seatmap_specified') . '</h1>';
  }
} else {
  echo '<h1>' . $message = Localization::getLocale('you_are_not_logged_in') . '</h1>';
}

function doRow(Row $row, string $image) {
  $seats = $row->getSeats();
  $x = $row->getX();
  $y = $row->getY();
  $i = 0;

  foreach ($seats as $seat) {
  	$xTr = $x;
  	$yTr = $y + ($i * (img_height + (img_margin)));
  	doSeat($seat, $image, $xTr, $yTr, $row);
  	$i++;
  }
}

function doSeat(Seat $seat, string $image, int $x, int $y, Row $row) {
  if ($seat->hasTicket()) {
    imagefilledrectangle($image, $x, $y, $x+img_width-1, $y+img_height-1, imagecolorallocate($image, 255, 0, 0));
  } else {
    imagefilledrectangle($image, $x, $y, $x+img_width-1, $y+img_height-1, imagecolorallocate($image, 0, 128, 0));
  }

  imagestring($image, 3, $x+5, $y+2, "R" . $row->getNumber(), imagecolorallocate($image, 255, 255, 255));
  imagestring($image, 3, $x+5, $y+16, "S" . $row->getNumber(), imagecolorallocate($image, 255, 255, 255));
}
?>
