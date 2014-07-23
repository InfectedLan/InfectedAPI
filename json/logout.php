<?php
if (isset($_SESSION['user'])) {
	unset($_SESSION['user']);
}

echo '{"result":true}'; //I mean, how can this even fail?
?>