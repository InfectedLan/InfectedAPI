<?php
require_once 'handlers/userhandler.php';
class SyslogEntry extends Object {
    private $source;
    private $severity;
    private $message;
    private $metadata;
    private $date;
    private $userId;

    public function getSource() {
	return $this->source;
    }

    public function getSeverity() {
	return $this->severity;
    }

    public function getMessage() {
	return $this->message;
    }

    public function getMetadata() {
	return json_decode($this->metadata);
    }

    public function getTimestamp() {
	return strtotime($this->date);
    }

    public function getUser() {
	if($this->userId == 0) {
	    return null;
	}
	return UserHandler::getUser($this->userId);
    }
}
?>