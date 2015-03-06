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

require_once 'session.php';
require_once 'mailmanager.php';
require_once 'handlers/eventhandler.php';

$result = false;
$message = null;

if (Session::isAuthenticated()) {
	$user = Session::getCurrentUser();
	
	if ($user->hasPermission('*') ||
		$user->hasPermission('chief.email')) {
		if (isset($_GET['userIdList']) &&
			isset($_GET['subject']) &&
			isset($_GET['message']) &&
			!empty($_GET['userIdList']) &&
			!empty($_GET['subject']) &&
			!empty($_GET['message'])) {
			$userIdList = explode(',', $_GET['userIdList']);
			$userList = array();
			
			// If the id's in user list is lower or equal to 0, we have to do something special here.
			if (count($userIdList) >= 1) {
				$value = $userIdList[0];
				$event = EventHandler::getCurrentEvent();

				// Match the given group of users, and build the array.
				if ($user->hasPermission('*')) {
					if ($value == 'all') {
						$userList = UserHandler::getUsers();
					} else if ($value == 'allMembers') {
						$userList = UserHandler::getMemberUsers();
					} else if ($value == 'allNonMembers') {
						$userList = UserHandler::getNonMemberUsers();
					} else if ($value == 'allWithTicket') {
						$userList = UserHandler::getParticipantUsers($event);
					} else if ($value == 'allWithTickets') {
						// Check which users have more than 1 ticket.
						foreach (UserHandler::getParticipantUsers($event) as $participant) {
							// If the participant user have more than 1 ticket, add him/her to the user list.
							if (count($participant->getTickets()) > 1) {
								array_push($userList, $participant);
							}
						}
					} else if ($value == 'allWithTicketLast3') {
						$userList = UserHandler::getPreviousParticipantUsers();
					}
				}
				
				if ($user->isGroupMember()) {
					if ($value == 'group') {
						$userList = $user->getGroup()->getMembers();
					}
				}
				
				// Build the userList from the given id's
				if (is_numeric($value)) {
					foreach ($userIdList as $userId) {
						array_push($userList, UserHandler::getUser($userId));
					}
				}
			}
			
			if (!empty($userList)) {
				// Sends emails to users in userList with the given subject and message.
				MailManager::sendEmails($userList, $_GET['subject'], $_GET['message']);
				
				// Format message differently when we're just sending email to one user.
				if (count($userList) <= 1) {
					$message = '<p>Din e-post ble sendt til den valgte brukeren.</p>';
				} else {
					$message = '<p>Din e-post ble sendt til de valgte brukerene.</p>';
				}
				
				$result = true;
			}
		} else {
			$message = '<p>Mangler informasjon, sjekk at du har fylt ut alle feltene.</p>';
		}
	} else {
		$message = '<p>Du har ikke tilgang til dette.</p>';
	}
} else {
	$message = '<p>Du er ikke logget inn.</p>';
} 

header('Content-Type: text/plain');
echo json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT);
?>