<?php
require_once 'handlers/userhandler.php';
class SyslogEntry extends Object {
    private $source;
    private $severity;
    private $message;
    private $metadata;
    private $date;
    private $user;

    const SEVERITY_INFO = 1; //Not dangerous, but informational
    const SEVERITY_ISSUE = 2; //Someone should check out this
    const SEVERITY_WARNING = 3; //Calm before the storm
    const SEVERITY_CRITICAL = 4; //HOLY FUCK THE SERVERS ARE BURNING

    public function getSource() {
	return $this->source;
    }

    public function getSeverity() {
	return $this->severity;
    }

    public function getMessage() {
	return $this->getMessage;
    }

    public function getMetadata() {
	return json_decode($this->metadata);
    }

    public function getTimestamp() {
	return strtotime($this->date);
    }

    public function getUser() {
	if($this->user == 0) {
	    return null;
	}
	return UserHandler::getUser($this->user);
    }
}
?>