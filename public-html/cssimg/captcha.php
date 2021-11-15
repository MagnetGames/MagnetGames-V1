<?php
	include '../src/globals.php';
	include '../src/captcha.php';
	
	if(isset($_GET["lock"]) && is_numeric($_GET["lock"]) && $_GET["lock"] > 0 && $_GET["lock"] <= 4 && isset($_SESSION['captcha_answer_' . $_GET["lock"]]))
	{
		// open the file in a binary mode
		$name = '../src/captcha/lock_' . $_SESSION['captcha_answer_' . $_GET["lock"]] . '.gif';
		$fp = fopen($name, 'rb');

		// send the right headers
		header("Content-Type: image/gif");
		header("Content-Length: " . filesize($name));

		// dump the picture and stop the script
		fpassthru($fp);
		exit;
	}elseif(isset($_GET["key"]) && is_numeric($_GET["key"]) && $_GET["key"] > 0 && $_GET["key"] <= 4)
	{
		// open the file in a binary mode
		$name = '../src/captcha/key_' . $_GET["key"] . '.gif';
		$fp = fopen($name, 'rb');

		// send the right headers
		header("Content-Type: image/gif");
		header("Content-Length: " . filesize($name));

		// dump the picture and stop the script
		fpassthru($fp);
		exit;
	}else
	{
		$IPAddress = GetIP();
		$IPAttempts = GetAttemptsFromIP($IPAddress);
		if($IPAttempts["captchaattempts"] >= 10)
		{
			echo json_encode(array('success' => 1, 'locked' => 1, 'index' => 4));
		}else
		{
			if(isset($_POST["key"]) && isset($_SESSION['captcha_locked']) && $_SESSION['captcha_locked'] == true)
			{
				if(is_numeric($_POST["key"]) && $_POST["key"] > 0 && $_POST["key"] <= 4)
				{
					if($_SESSION['captcha_index'] == 0)
					{
						if($_POST["key"] == $_SESSION['captcha_answer_1'])
						{
							$_SESSION['captcha_index'] = 1;
							$_SESSION['captcha_answer_1'] = 0;
							echo json_encode(array('success' => 1, 'locked' => 1, 'index' => 1));
						}else
						{
							GenLock();
							AddIPCaptchaAttempt($IPAddress);
							if($IPAttempts["captchaattempts"] >=9)
							{
								echo json_encode(array('success' => 1, 'locked' => 1, 'index' => 4));
							}else
							{
								echo json_encode(array('success' => 0, 'locked' => 1, 'index' => 0));
							}
						}
					}elseif($_SESSION['captcha_index'] == 1)
					{
						if($_POST["key"] == $_SESSION['captcha_answer_2'])
						{
							$_SESSION['captcha_index'] = 2;
							$_SESSION['captcha_answer_2'] = 0;
							echo json_encode(array('success' => 1, 'locked' => 1, 'index' => 2));
						}else
						{
							GenLock();
							AddIPCaptchaAttempt($IPAddress);
							if($IPAttempts["captchaattempts"] >=9)
							{
								echo json_encode(array('success' => 1, 'locked' => 1, 'index' => 4));
							}else
							{
								echo json_encode(array('success' => 0, 'locked' => 1, 'index' => 0));
							}
						}
					}elseif($_SESSION['captcha_index'] == 2)
					{
						if($_POST["key"] == $_SESSION['captcha_answer_3'])
						{
							$_SESSION['captcha_index'] = 3;
							$_SESSION['captcha_answer_3'] = 0;
							echo json_encode(array('success' => 1, 'locked' => 1, 'index' => 3));
						}else
						{
							GenLock();
							AddIPCaptchaAttempt($IPAddress);
							if($IPAttempts["captchaattempts"] >=9)
							{
								echo json_encode(array('success' => 1, 'locked' => 1, 'index' => 4));
							}else
							{
								echo json_encode(array('success' => 0, 'locked' => 1, 'index' => 0));
							}
						}
					}elseif($_SESSION['captcha_index'] == 3)
					{
						if($_POST["key"] == $_SESSION['captcha_answer_4'])
						{
							$_SESSION['captcha_index'] = 0;
							$_SESSION['captcha_answer_4'] = 0;
							$_SESSION['captcha_locked'] = false;
							echo json_encode(array('success' => 1, 'locked' => 0, 'index' => 0));
						}else
						{
							GenLock();
							AddIPCaptchaAttempt($IPAddress);
							if($IPAttempts["captchaattempts"] >=9)
							{
								echo json_encode(array('success' => 1, 'locked' => 1, 'index' => 4));
							}else
							{
								echo json_encode(array('success' => 0, 'locked' => 1, 'index' => 0));
							}
						}
					}
				}else
				{
					GenLock();
					AddIPCaptchaAttempt($IPAddress);
					if($IPAttempts["captchaattempts"] >=9)
					{
						echo json_encode(array('success' => 1, 'locked' => 1, 'index' => 4));
					}else
					{
						echo json_encode(array('success' => 0, 'locked' => 1, 'index' => 0));
					}
				}
			}else
			{
				//echo 'go away';
				echo '<meta http-equiv="Refresh" content="0; url=../index.php?pageid=403">';
			}
		}
	}
?>