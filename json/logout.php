<?php
	session_start();
	session_destroy();
	echo '{"result":"success"}'; //I mean, how can this even fail?
?>