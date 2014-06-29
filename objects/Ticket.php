<?php
class Ticket
{
	private $id;
	private $event;
	private $owner;
	private $type;
	private $seat;
	private $user;
	private $seater;

	/*
	 * Ticket - implementation of backend ticket db.
	 * 
	 * Id: Unique id of ticket
	 * Event_Id: Id of event ticket is connected to
	 * Owner: User that owns the ticket
	 * Type: Ticket type. Object.
	 * Seat: Object of seat ticket is seated on
	 * User: User account that will be using the ticket
	 * Seater: User account that can seat this ticket
	 */
	public function Ticket($id, $event, $owner, $type, $seat, $user, $seater)
	{
		$this->id = $id;
		$this->event = $event;
		$this->owner = $owner;
		$this->type = $type;
		$this->seat = $seat;
		$this->user = $user;
		$this->seater = $seater;
	}

	/*
	 * Returns the unique id for the ticket
	 */
	public function getId()
	{
		return $this->id;
	}

	/*
	 * Returns the event this ticket is for
	 */
	public function getEvent()
	{
		return $this->event;
	}

	/*
	 * Rerturns the owner of this ticket.
	 *
	 * The owner is the user account that purchased the ticket.
	 */
	public function getOwner()
	{
		return $this->owner;
	}

	/*
	 * Returns the ticket type
	 */
	public function getType()
	{
		return $this->type;
	}

	/*
	 * Returns the seat that this ticket is seated at
	 */
	public function getSeat()
	{
		return $this->seat;
	}

	/*
	 * Returns the user of this ticket.
	 *
	 * The user is the person who will be using the ticket during the party
	 */
	public function getUser()
	{
		return $this->user;
	}

	/*
	 * Returns the seater of this ticket.
	 *
	 * The seater is the user account that is allowed to decide what seat this ticket is seated on.
	 */
	public function getSeater()
	{
		return $this->seater;
	}
}
?>