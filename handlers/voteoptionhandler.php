<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/voteoption.php';

class VoteOptionHandler {
    /*
     * Get a vote option by the internal id.
     */
    public static function getVoteOption($id) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` 
		                         WHERE `id` = \'' . $id . '\';');
        
        $mysql->close();
		
		return $result->fetch_object('VoteOption');
    }

    /*
     * Get a vote option for a specified compo.
     */
    public static function getVoteOptionsForCompo($compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` 
                                 WHERE `compoId` = '. $mysql->real_escape_string($compo->getId()) . ';');
        
        $mysql->close();

        $voteOptionList = array();

        while ($object = $result->fetch_object('VoteOption')) {
            array_push($voteOptionList, $object);
        }

        return $voteOptionList;
    }

    /*
     * Returns true if specified vote option is voted for the specified match.
     */
    public static function isVoted($voteOption, $match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_votes . '` 
                                 WHERE `voteOptionId` = '. $mysql->real_escape_string($voteOption->getId()) . '
								 AND `consumerId` = ' . $mysql->real_escape_string($match->getId()) . ';');
        
        $mysql->close();                         
        
        $row = $result->fetch_array();

        return $row ? true : false;
    }
}
?>