<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/voteoption.php';

class VoteOptionHandler {
    public static function getVoteOption($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` WHERE `id` = \'' . $id . '\';');
        
        $mysql->close();
        
        $row = $result->fetch_array();
        
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

        while ($row = $result->fetch_array()) {
            array_push($returnArray, self::getVoteOption($row['id']));
        }
        
        return $returnArray;
    }

    public static function isVoted($voteOption, $match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_votes . '` 
                                 WHERE `voteOptionId` = '. $mysql->real_escape_string($voteOption->getId()) . ' AND `consumerId` = ' . $mysql->real_escape_string($match->getId()) . ';');
        
        $mysql->close();                         
        
        $row = $result->fetch_array();

        return $row ? true : false;
    }
}
?>