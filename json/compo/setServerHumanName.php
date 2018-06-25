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
require_once 'database.php';
require_once 'localization.php';
require_once 'handlers/compohandler.php';
require_once 'handlers/serverhandler.php';

$result = false;
$message = null;
$data = null;

if (Session::isAuthenticated()) {
  $user = Session::getCurrentUser();

  if ($user->hasPermission('compo.edit')) {
    if (isset($_GET['id']) &&
      isset($_GET['humanName'])) {
      $server = ServerHandler::getServer($_GET['id']);

      if ($server != null) {
        ServerHandler::setHumanName($server, $_GET['humanName']);
        $result = true;
      } else {
        $message = Localization::getLocale('this_server_does_not_exist');
      }
    } else {
      $message = Localization::getLocale('you_have_not_filled_out_the_required_fields');
    }
  } else {
    $message = Localization::getLocale('you_do_not_have_permission_to_do_that');
  }
} else {
  $message = Localization::getLocale('you_are_not_logged_in');
}

header('Content-Type: application/json');

if ($result) {
  echo json_encode(array('result' => $result), JSON_PRETTY_PRINT);
} else {
  echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
}

Database::cleanup();
?>
