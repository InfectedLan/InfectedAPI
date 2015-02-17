<?php
require_once 'handlers/eventhandler.php';
require_once 'objects/object.php';

class EventObject extends Object {
	protected $eventId;
	
	/*
	 * Returns the event of this object.
	 */ 
	public function getEvent() {
		return EventHandler::getEvent($this->eventId);
	}
}
?>