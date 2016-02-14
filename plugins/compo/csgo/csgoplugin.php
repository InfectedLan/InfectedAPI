<?php
require_once 'objects/compoplugin.php';
class CsgoPlugin extends CompoPlugin {
    public function getCustomMatchInformation(Match $match) { //Called for each current match on the crew page
        return null;
    }
    public function hasVoteScreen() {
	return true;
    }
}
?>