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

require_once 'mailmanager.php';
require_once 'handlers/notehandler.php';
require_once 'objects/user.php';
require_once 'objects/group.php';
require_once 'objects/application.php';
require_once 'utils/dateutils.php';

class NotificationManager {
	/*
	 * This function will be automatically called on an hourly basis, and should be used to send out automatic notifications.
	 */
	public static function checkForNotifications() {
		// Check for notes notifications.
		foreach (NoteHandler::getNotesReachedNotificationTime() as $note) {
			if ($note->hasOwner() || $note->hasUser()) {
				$message = [];
				$message[] = '<!DOCTYPE html>';
				$message[] = '<html>';
					$message[] = '<body>';
						$message[] = '<h3>Hei!</h3>';
						$message[] = '<p>Dette er en påmindelse for ditt gjøremål som nærmer seg fristen, dette må være ferdig ' . DateUtils::getDayFromInt(date('w', $note->getAbsoluteTime())) . ' den ' . date('d', $note->getAbsoluteTime()) . '. ' . DateUtils::getMonthFromInt(date('m', $note->getAbsoluteTime())) . ' kl. ' . date('H:i', $note->getAbsoluteTime()) . '.<p>';
						$message[] = '<p><b>Navn på gjøremålet:</b> ' . $note->getTitle() . '</p>';
						$message[] = '<p><b>Detaljer:</b> <br>';
						$message[] = wordwrap($note->getContent(), 75, '<br>') . '</p>';
						$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
					$message[] = '</body>';
				$message[] = '</html>';

				MailManager::sendEmail($note->getUser(), 'Du har et gjøremål som nærmer seg fristen', implode("\r\n", $message));
				$note->setNotified(true);
			}
		}
	}

	/*
	 * Sends an mail to the users e-mail address with status information.
	 */
	public function sendApplicationCreatedNotification(Application $application) {
		$group = $application->getGroup();
		$userList = [];

		if ($group->hasLeader()) {
			$userList[] = $group->getLeader();
		}

		if (!empty($userList)) {
			$message = [];
			$message[] = '<!DOCTYPE html>';
			$message[] = '<html>';
				$message[] = '<body>';
					$message[] = '<h3>Hei!</h3>';
					$message[] = '<p>Du har fått en ny søknad til crewet ditt (' . $group->getTitle() . ') fra ' . $application->getUser()->getFullName() . '<p>';
					$message[] = '<p>Klikk <a href="https://crew.infected.no/v2/index.php?page=application&id=' . $application->getId() . '">her</a> for å se den.</p>';
					$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
				$message[] = '</body>';
			$message[] = '</html>';

			return MailManager::sendEmails($userList, 'Ny søknad til ' . $group->getTitle() . ' crew', implode("\r\n", $message));
		}
	}

	/*
	 * Sends an mail to the users e-mail address with status information.
	 */
	public function sendApplicationAccpetedNotification(Application $application) {
		$message = [];
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>Din crew søknad til ' . $application->getGroup()->getTitle() . ' crew har blitt godkjent.</p>';
				$message[] = 'Du kan nå logge inn på <a href="https://crew.infected.no/">Infected Crew</a> å bli kjent med det nye crewet ditt.<br>';
				$message[] = 'Ta deg tid til å gå igjennom profilen din å sjekk at du har oppgitt alle og riktige opplysninger da dette blir brukt til adgangskort osv. under arrangementet.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';

		return MailManager::sendEmail($application->getUser(), 'Din Infected Crew søknad har blitt oppdatert', implode("\r\n", $message));
	}

	/*
	 * Sends an mail to the users e-mail address with status information.
	 */
	public function sendApplicationRejectedNotification(Application $application, string $comment) {
		$message = [];
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>Din crew søknad til ' . $application->getGroup()->getTitle() . ' crew har blitt avvist.<br>';
				$message[] = 'Grunnen var: ' . $comment . '</p>';
				$message[] = '<p>Du er velkommen til å søke til et annet crew eller prøve på nytt neste gang.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';

		return MailManager::sendEmail($application->getUser(), 'Din Infected Crew søknad har blitt oppdatert', implode("\r\n", $message));
	}

	/*
	 * Sends an mail to the users e-mail address with status information.
	 */
	public function sendApplicationQueuedNotification(Application $application) {
		$message = [];
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>Din crew søknad til ' . $application->getGroup()->getTitle() . ' crew har blitt satt i kø.<br>';
				$message[] = 'Dette betyr at crewet du søkte for øyeblikket er fullt, men at er en aktuell kandidat, <br>';
				$message[] = 'søknaden din vil bli godkjent senere dersom det blir behov for flere medlemmer.</p>';
				$message[] = '<p>I mellomtiden er du velkommen til å søke deg inn i andre crew, men merk at det da er den første godkjente søknaden som bil bli godkjent.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';

		return MailManager::sendEmail($application->getUser(), 'Din Infected Crew søknad har blitt oppdatert', implode("\r\n", $message));
	}

	/*
	 * Sends a notification to the users e-mail address with purchase information.
	 */
	public function sendPurchaseCompleteNotification(User $user, string $reference) {
		$message = [];
		$message[] = '<!DOCTYPE html>';
		$message[] = '<html>';
			$message[] = '<body>';
				$message[] = '<h3>Hei!</h3>';
				$message[] = '<p>Takk for din bestilling.<p>';
				$message[] = '<p>Bestillingsreferansen din er: <b>' . $reference . '</b>';
				$message[] = '<p>Du kan nå plassere deg ved å trykke <a href="https://tickets.infected.no/v2/index.php?page=viewSeatmap">her</a>.</p>';
				$message[] = '<p>Med vennlig hilsen <a href="http://infected.no/">Infected</a>.</p>';
			$message[] = '</body>';
		$message[] = '</html>';

		return MailManager::sendEmail($user, 'Takk for ditt kjøp av billett til Infected.', implode("\r\n", $message));
	}
}
?>
