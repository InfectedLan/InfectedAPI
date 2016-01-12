<?php
require_once 'settings.php';
require_once 'database.php';
require_once 'objects/syslogentry.php';
class SyslogHandler {
    
    const SEVERITY_INFO = 1; //Not dangerous, but informational
    const SEVERITY_ISSUE = 2; //Someone should check out this
    const SEVERITY_WARNING = 3; //Calm before the storm
    const SEVERITY_CRITICAL = 4; //HOLY FUCK THE SERVERS ARE BURNING

    public static function getSyslogEntry($id) {
	$database = Database::open(Settings::db_name_infected);

	$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_syslogs . '`
																WHERE `id` = \'' . $database->real_escape_string($id) . '\';');

	$database->close();

	return $result->fetch_object('SyslogEntry');
    }

    public static function getLastEntries($count) {
	$database = Database::open(Settings::db_name_infected);

	$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_syslogs . '` ORDER BY `id` ASC LIMIT ' . $database->real_escape_string($count) . ';');

	$database->close();

	$syslogList = [];

	while ($object = $result->fetch_object('SyslogEntry')) {
	    $syslogList[] = $object;
	}

	return $syslogList;
    }

    public static function getLastEntriesBySource($source, $count) {
	$database = Database::open(Settings::db_name_infected);

	$result = $database->query('SELECT * FROM `' . Settings::db_table_infected_syslogs . '` WHERE `source` LIKE \'' . $database->real_escape_string($source) . '\' ORDER BY `id` ASC LIMIT ' . $database->real_escape_string($count) . ';');

	$database->close();

	$syslogList = [];

	while ($object = $result->fetch_object('SyslogEntry')) {
	    $syslogList[] = $object;
	}

	return $syslogList;
    }

    public static function log($message, $source, $user = null, $severity = self::SEVERITY_INFO, $metadata = array()) {
	$date = date('Y-m-d H:i:s');
	$database = Database::open(Settings::db_name_infected);
	$userId = ($user == null ? 0 : $user->getId());
	$query = 'INSERT INTO `' . Settings::db_table_infected_syslogs . '`(`source`, `severity`, `message`, `metadata`, `date`, `userId`)  VALUES (\'' .
				   $database->real_escape_string($source) . '\', \'' .
				   $database->real_escape_string($severity) . '\', \'' .
				   $database->real_escape_string($message) . '\', \'' .
				   $database->real_escape_string(json_encode($metadata)) . '\', \'' .
				   $database->real_escape_string($date) . '\', \'' .
	    $database->real_escape_string($userId) . '\');';
	$result = $database->query($query);
    }

    public static function getSeverityString($severity) {
	switch($severity) {
	case self::SEVERITY_INFO:
	    return "Info";
	case self::SEVERITY_ISSUE:
	    return "Issue";
	case self::SEVERITY_WARNING:
	    return "Warning";
	case self::SEVERITY_CRITICAL:
	    return "Critical";
	}
    }
}
?>