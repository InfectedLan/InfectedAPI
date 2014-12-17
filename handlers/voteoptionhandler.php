<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/voteoption.php';

class VoteOptionHandler {
    public static function getVoteOption($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` WHERE `id` = \'' . $id . '\';');
        
        $row = mysqli_fetch_array($result);
        
        $mysql->close();
        
        if ($row) {
            return new VoteOption($row['id'], 
                                  $row['compoId'], 
                                  $row['thumbnailUrl'], 
                                  $row['name']);
        }
    }

    public static function getVoteOptionsForCompo($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` 
                                 WHERE `compoId` = '. $mysql->real_escape_string($compo->getId()) . ';');
        
        $mysql->close();

        $returnArray = array();

        while ($row = mysqli_fetch_array($result)) {
            array_push($returnArray, self::getVoteOption($row['id']));
        }
        
        return $returnArray;
    }

    public static function isVoted($voteOption, $match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_votes . '` 
                                 WHERE `voteOptionId` = '. $mysql->real_escape_string($voteOption->getId()) . ' AND `consumerId` = ' . $mysql->real_escape_string($match->getId()) . ';');

        $row = mysqli_fetch_array($result);

        $mysql->close();

        return $row ? true : false;
    }
}
?>