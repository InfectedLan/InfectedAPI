<?php
	require_once 'session.php';
	require_once 'handlers/seatmaphandler.php';

	$result = false;
	$message = null;

	if (Session::isAuthenticated()) {
		$user = Session::getCurrentUser();
		if ($user->hasPermission('admin.seatmap') ||
			$user->hasPermission('admin')) {
			if(isset($_POST["seatmapId"]))
			{
				$seatmap = SeatmapHandler::getSeatmap($_POST["seatmapId"]);
				if(isset($seatmap))
				{
					//TODO cleanup if the upload "overwrites" another image
					//Validate image
					$allowedExts = array("jpeg", "jpg", "png");
					$temp = explode(".", $_FILES["bgImageFile"]["name"]);
					$extension = strtolower(end($temp));

					if( ($_FILES["bgImageFile"]["type"] == "image/jpeg") || 
					    ($_FILES["bgImageFile"]["type"] == "image/jpg") || 
					    ($_FILES["bgImageFile"]["type"] == "image/x-png") || 
					    ($_FILES["bgImageFile"]["type"] == "image/png"))
					{
						if (($_FILES["bgImageFile"]["size"] < 7000000))
						{
							if(in_array($extension, $allowedExts))
							{
								if ($_FILES["bgImageFile"]["error"] == 0)
								{
									$name = md5(time() . "yoloswag");
									move_uploaded_file($_FILES["bgImageFile"]["tmp_name"], "../" . $name . "." . $extension);

									SeatmapHandler::setBackground($seatmap, $name . "." . $extension);

									$result = true;
								}
								else
								{
									$message = "Det skjedde en feil under opplastingen av bildet!";
								}
							}
							else
							{
								$message = "Feil filformat!";
							}
						}
						else
						{
							$message = "Bildet er for stort!";
						}
					}
					else
					{
						$message = "Filformatet er ikke riktig!";
					}
				}
				else
				{
					$message = "Seatmappet finnes ikke!";
				}
			}
			else
			{
				$message = "SeatmapId er ikke satt!";
			}
		}
		else
		{
			$message = "Du har ikke tillatelse til å legge til en rad!";
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