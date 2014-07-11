<?php
	session_start();
	session_destroy();
	echo '{"result":true}'; //I mean, how can this even fail?
?>