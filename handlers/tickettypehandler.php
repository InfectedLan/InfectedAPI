<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/tickettype.php';

class TicketTypeHandler {
    public static function getTicketType($id) {
        $mysql = MySQL::open(Settings::db_name_infected_tickets);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_tickets_tickettypes . '` 
                               WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
                                      
        $row = mysqli_fetch_array($result);

        $mysql->close();

        if ($row) {
            return new TicketType($row['id'], 
                                  $row['humanName'],
                                  $row['price'],
                                  $row['internalName']);
        }
    }
}
?>