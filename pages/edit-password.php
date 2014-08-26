<?php
require_once 'session.php';

if (Session::isAuthenticated()) {
	echo '<h3>Endre passord</h3>';
	
	echo '<script src="../api/scripts/edit-password.js"></script>';
	echo '<form class="edit-password" method="post">';
		echo '<table>';
			echo '<tr>';
				echo '<td>Gammelt passord:</td>';
				echo '<td><input type="password" name="oldPassword" required autofocus></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Nytt passord:</td>';
				echo '<td><input type="password" name="newPassword" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td>Gjenta nytt passord:</td>';
				echo '<td><input type="password" name="confirmNewPassword" required></td>';
			echo '</tr>';
			echo '<tr>';
				echo '<td><input type="submit" value="Lagre"></td>';
			echo '</tr>';
		echo '</table>';
	echo '</form>';
} else {
	echo '<p>Du er ikke logget inn!</p>';
}
?>