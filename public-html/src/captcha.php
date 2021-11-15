<?php
	//Generate random lock
	function GenLock()
	{
		$_SESSION['captcha_answer_1'] = rand(1, 4);
		$_SESSION['captcha_answer_2'] = rand(1, 4); 
		$_SESSION['captcha_answer_3'] = rand(1, 4);
		$_SESSION['captcha_answer_4'] = rand(1, 4);
		$_SESSION['captcha_index'] = 0;
		$_SESSION['captcha_locked'] = true;
		while($_SESSION['captcha_answer_1'] == $_SESSION['captcha_answer_2'] && $_SESSION['captcha_answer_1'] == $_SESSION['captcha_answer_3'] && $_SESSION['captcha_answer_1'] == $_SESSION['captcha_answer_4'])
		{
			//Don't want all to be the same
			$_SESSION['captcha_answer_1'] = rand(1, 4);
			$_SESSION['captcha_answer_2'] = rand(1, 4); 
			$_SESSION['captcha_answer_3'] = rand(1, 4);
			$_SESSION['captcha_answer_4'] = rand(1, 4);
		}
	}
	function DrawCaptcha()
	{
		$IPAddress = GetIP();
		$IPAttempts = GetAttemptsFromIP($IPAddress);
		if(!isset($_SESSION['captcha_locked']))
		{
			GenLock();
		}
			$pagecontent = '<center><div style="border: 1px solid #000000;background: url(./cssimg/captchabg.gif);width: 400px;height: 160px;">';
			$pagecontent .= '<b><span style="color: #FFFF00;text-shadow: 1px 1px #000000;">Captcha Lock Puzzle!</span></b><br>';
			$pagecontent .= '<img src="./cssimg/captcha.php?lock=1" width="32px" id="padlock_1">';
			$pagecontent .= '<img src="./cssimg/captcha.php?lock=2" width="32px" id="padlock_2">';
			$pagecontent .= '<img src="./cssimg/captcha.php?lock=3" width="32px" id="padlock_3">';
			$pagecontent .= '<img src="./cssimg/captcha.php?lock=4" width="32px" id="padlock_4">';
			if(isset($_SESSION['captcha_locked']) && $_SESSION['captcha_locked'])
			{
				if($IPAttempts["captchaattempts"] >= 10)
				{
					$pagecontent .= '<br><span id="captchahint" style="color: #FF0000;text-shadow: 1px 1px #000000;">' . "Sorry, but you've ran out of attempts to complete the puzzle!<br>Please try again in 4 hours." . '</span><br>';
					$pagecontent .= '<img src="./cssimg/sad.png"></img>';
				}else
				{
					if($_SESSION['captcha_index'] > 0)
					{
						$pagecontent .= '<br><span id="captchahint" style="color: #00FF00;text-shadow: 1px 1px #000000;"><b>Congrats!<br>Only ' . (4 - $_SESSION['captcha_index']) . ' more to go.</b><br></span><br>';
					}else
					{
						$pagecontent .= '<br><span id="captchahint" style="color: #FFFFFF;text-shadow: 1px 1px #000000;">Please unlock the locks with the correct keys from left-to-right before proceeding!<br></span><br>';
					}
					
					$pagecontent .= '<div id="captchakeys">';
						$pagecontent .= '<img src="./cssimg/captcha.php?key=1" width="22px" class="Clickable" id="key_1" onclick="SubmitKey(1);"> ';
						$pagecontent .= '<img src="./cssimg/captcha.php?key=2" width="22px" class="Clickable" id="key_2" onclick="SubmitKey(2);"> ';
						$pagecontent .= '<img src="./cssimg/captcha.php?key=3" width="22px" class="Clickable" id="key_3" onclick="SubmitKey(3);"> ';
						$pagecontent .= '<img src="./cssimg/captcha.php?key=4" width="22px" class="Clickable" id="key_4" onclick="SubmitKey(4);">';
					$pagecontent .= '</div>';
					$pagecontent .= '<div id="captchaface" style="display: none;">';
						$pagecontent .= '<img src="./cssimg/smiley.png"></img>';
					$pagecontent .= '</div>';
					$pagecontent .= '<div id="captchasadface" style="display: none;">';
						$pagecontent .= '<img src="./cssimg/sad.png"></img>';
					$pagecontent .= '</div>';
				}
			}else
			{
				$pagecontent .= '<br><span id="captchahint" style="color: #00FF00;text-shadow: 1px 1px #000000;"><b>Congrats!<br>You may now proceed!</b><br></span><br>';
				$pagecontent .= '<img src="./cssimg/smiley.png"></img>';
			}
		$pagecontent .= '</div></center><br>';
		return $pagecontent;
	}
	
?>