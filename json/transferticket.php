<?php
require_once 'session.php';
require_once 'handlers/tickethandler.php';

if(!Session::isAuthenticated())
{
	echo '{"result":false, "message":"You arent authenticated!"}';
	return;
}

$ticketid = $_GET["id"];
$ticket = TicketHandler::getTicket($ticketid);

if(!isset($ticket))
{
	echo '{"result":false, "message":"Ugyldig bilett"}';
	return;
}
$me = Session::getUser();
if($me->getId() == $ticket->getOwner()->getId())
{
	$target = $_GET["target"];
	if(!isset($target))
	{
		echo '{"result":false, "message":"Felt mangler! Trengeer mål!"}';
		return;
	}
	else
	{
		$target = UserHandler::getUser($target);
		if(!isset($target))
		{
			echo '{"result":false, "message":"Målbrukeren eksisterer ikke!"}';
			return;
		}
		else
		{
			TicketHandler::transferTicket($ticket, $target);
			echo '{"result":true, "message":"Biletten er overført"}';
			return;
		}
	}
}
else
{
	echo '{"result":false, "message":"Du eier ikke biletten!"}';
	return;
}
?>