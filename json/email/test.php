<?php
require_once 'session.php';
require_once 'mailmanager.php';
require_once 'handlers/userhandler.php';

$userList = UserHandler::getPreviousParticipantUsers();

var_dump($userList);
?>