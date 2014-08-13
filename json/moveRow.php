<?php
	require_once 'session.php';
	require_once 'handlers/rowhandler.php';
	require_once 'handlers/seatmaphandler.php';

	$result = false;
	$message = null;
	$id = null;

	if (Session::isAuthenticated()) {
		$user = Session::getCurrentUser();
		if ($user->hasPermission('admin.seatmap') ||
			$user->hasPermission('admin')) {
			if(isset($_GET["row"]))
			{
				$row = RowHandler::getRow($_GET["row"]);
				if(isset($row))
				{
					if( isset( $_GET["x"] ) && isset( $_GET["y"] ) )
					{
						RowHandler::moveRow($row, $_GET["x"], $_GET["y"]);
						$result = true;
					}
					else
					{
						$message = "Posisjonen er ikke satt!";
					}
				}
				else
				{
					$message = "Raden eksisterer ikke!";
				}
			}
			else
			{
				$message = "Raden er ikke satt!";
			}
		}
		else
		{
			$message = "Du har ikke tillatelse til å flytte en rad!";
		}
	}
	else
	{
		$message = "Du må logge inn først!";
	}

	if($result)
	{
		echo json_encode(array('result' => $result));
	}
	else
	{
		echo json_encode(array('result' => $result, 'message' => $message));
	}
?>