<?php
require_once 'settings.php';
require_once 'mysql.php';
require_once 'objects/voteoption.php';
require_once 'objects/compo.php';
require_once 'objects/match.php';

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
    public static function getVoteOptionsForCompo(Compo $compo) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);
        
        $result = $mysql->query('SELECT * FROM `' . Settings::db_table_infected_compo_voteoptions . '` 
                                 WHERE `compoId` = '. $compo->getId() . ';');
        
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
    public static function isVoted(VoteOption $voteOption, Match $match) {
        $mysql = MySQL::open(Settings::db_name_infected_compo);

        $result = $mysql->query('SELECT `id` FROM `' . Settings::db_table_infected_compo_votes . '` 
                                 WHERE `voteOptionId` = '. $voteOption->getId() . '
								 AND `consumerId` = ' . $match->getId() . ';');
        
        $mysql->close();                         
        
        return $result->num_rows > 0;
    }
}
?>