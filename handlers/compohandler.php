<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/compo.php';
require_once 'handlers/clanhandler.php';

class CompoHandler {
    public static function getCompo($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_compos . '` 
                                      WHERE `id` = \'' . $mysql->real_escape_string($id) . '\';');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        if ($row) {
            return new Compo($row['id'], 
                             $row['startTime'], 
                             $row['registrationDeadline'], 
                             $row['name'],
                             $row['desc'], 
                             $row['event'], 
                             $row['teamSize'], 
                             $row['tag']);
        }
    }
    public static function getComposForEvent($event) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_compos . '` 
                                      WHERE `event` = \'' . $event->getId() . '\';');

        $compoList = array();

        while ($row = mysqli_fetch_array($result)) {
            array_push($compoList, self::getCompo($row['id']));
        }

        $mysql->close();

        return $compoList;
    }

    public static function getClans($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_participantof . '` 
                                      WHERE `compoId` = \'' . $mysql->real_escape_string($compo->getId()) . '\';');

        $clanList = array();

        while ($row = mysqli_fetch_array($result)) {
            $clan =  ClanHandler::getClan($row['clanId']);
            array_push($clanList, $clan);
        }

        $mysql->close();
        
        return $clanList;
    }
}
?>