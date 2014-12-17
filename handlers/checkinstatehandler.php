<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/checkinstate.php';

class CheckinStateHandler {
    public static function getCheckinState($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_checkinstate . '` 
                                      WHERE `id` = \'' . $id . '\';');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        if ($row) {
            return new CheckinState($row['id'], 
                                    $row['ticketId'],
                                    $row['userId']);
        }
    }

    public static function isCheckedIn($ticket) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_checkinstate . '` 
                                      WHERE `ticketId` = ' . $mysql->real_escape_string($ticket->getId()) . ';');

        $row = mysqli_fetch_array($result);
        
        $mysql->close();

        return $row ? true : false;
    }

    public static function checkIn($ticket) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $reuslt = $mysql->query('INSERT INTO `' . Settings::db_table_infected_tickets_checkinstate . '` (`ticketId`) 
                                      VALUES (\'' . $mysql->real_escape_string($ticket->getId()) . '\');');

        $mysql->close();
    }
}
?>