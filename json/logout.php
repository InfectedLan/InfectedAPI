<?php
require_once '../includes.php';

session_destroy();

echo '{"result":true}'; //I mean, how can this even fail?
?>