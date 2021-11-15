<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require './src/phpmailer/Exception.php';
	require './src/phpmailer/PHPMailer.php';
	require './src/phpmailer/SMTP.php';
	/*
	Vanilla Emails
	
	
	
	//Base template for emails
	function SendEmail($email, $subject, $content)
	{
		$debugmode = false; //Exports to a cache file if true. Good for testing
		$finalstring = file_get_contents('./src/pages/emailheader.html');
		$finalstring = $finalstring . $content;
		$finalstring = $finalstring . file_get_contents('./src/pages/emailfooter.html');

		if($debugmode)
		{
			$debugfile = fopen('./src/cache/' . $email . '.html', 'w');
			fwrite($debugfile, $finalstring);
			fclose($debugfile);
		}else
		{
			mail($email, $subject, $finalstring, "MIME-Version: 1.0\r\nContent-type:text/html; charset=ISO-8859-1\r\nFrom: <admin@mailer.magnet-games.com>\r\n");
		}
	}
	*/
	
	//PHPMailer port
	function SendEmail($email, $subject, $content)
	{
		$debugmode = false; //Exports to a cache file if true. Good for testing
		$finalstring = file_get_contents('./src/pages/emailheader.html');
		$finalstring = $finalstring . $content;
		$finalstring = $finalstring . file_get_contents('./src/pages/emailfooter.html');

		if($debugmode)
		{
			$debugfile = fopen('./src/cache/' . $email . '.html', 'w');
			fwrite($debugfile, $finalstring);
			fclose($debugfile);
		}else
		{
			//SMTP account!
			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->SMTPAuth = true;
			$mail->Host = "host"; //Modified for 2021 Source Release
			$mail->Port = 587;
			$mail->Username = 'username';
			$mail->Password = 'password';
			$mail->setFrom('admin@mail.magnet-games.com', 'Magnet Games', 0);
			$mail->addAddress($email);
			$mail->Subject = $subject;
			$mail->MsgHTML($finalstring);
			if(!$mail->send()) {
			  echo 'Message was not sent.';
			  echo 'Mailer error: ' . $mail->ErrorInfo;
			}
		}
	}
	
	
	//Send game approval emails
	function SendEmail_GameApproval($userid, $gametitle, $gameid)
	{
		$receiveuser = GetUserFromID($userid);
		$subject = 'Your game suggestion was approved!';
		$content = '<center>';
		$content .= '<h1 style="padding: 0;margin: 0;">' . $subject . '</h1><hr>';
		$content .= 'Hi <b>' . $receiveuser['username'] . '!</b><br>';
		$content .= 'Your game suggestion <b>"' . $gametitle . '"</b> was approved by an admin and is <a href="https://magnet-games.com/index.php?gameid=' . $gameid . '">viewable on the site!</a><br>';
		$content .= 'Thank you for your contribution!<br><img src="https://magnet-games.com/cssimg/smiley.png">';
		$content .= '</center>';
		SendEmail($receiveuser['useremail'], $subject, $content);
	}
	
	//Send admin comments
	function SendEmail_Admin($receiveuser, $message)
	{
		$subject = 'An admin sent you a message!';
		$content = '<center>';
		$content .= '<h1 style="padding: 0;margin: 0;">Hi ' . $receiveuser['username'] . '! An admin sent you a message!</h1><hr>';
		$content .= '<div style="margin: auto;padding:6px;max-height: 400px;overflow-y: auto;background: #CCFFFF;border: 1px solid #009999;display: inline-block;">' . $message . '</div><br>';
		$content .= 'Please do not reply to this email, if you need to contact us, you can send an email to <a href="mailto:MagnetGames2001@gmail.com">MagnetGames2001@gmail.com</a>';
		$content .= '</center>';
		SendEmail($receiveuser['useremail'], $subject, $content);
	}
	//Send comment replies
	function SendEmail_Reply($postid)
	{
		$maincomment = GetCommentFromPostID($postid);
		$sourcecomment = GetCommentFromPostID($maincomment['com_replyid']);
		
		$receiveuser = GetUserFromID($sourcecomment['com_userid']);
		//User has email notifications enabled...
		if($receiveuser['usernotify'] && $receiveuser['isbanned'] == false)
		{
			$senduser = GetUserFromID($maincomment['com_userid']);
			//Make sure not to send emails if you're replying to yourself
			if($senduser['userid'] <> $receiveuser['userid'])
			{
				$date = getdate($maincomment['com_date']);
				$content = '<center><h1 style="padding: 0;margin: 0;"><a href="https://magnet-games.com/profile.php?username=' . $senduser['username'] . '">' . $senduser['username'] . '</a> has replied to your comment!</h1></center><hr>'.
				'<div style="margin-top: auto;margin-left: auto;margin-right: auto;margin-bottom: 0.5em;background: #FFFFFF;width: 100%;border: 0.25em outset #00AAAA;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box; ">'.
					'<div>'.
						'<div style="padding:6px;background: #99DDDD;border-bottom: 1px solid #009999;overflow: auto;">'.
							'<center>'.
								RenderBadges($senduser, "https://magnet-games.com") . '<b><a href="https://magnet-games.com/profile.php?username=' . $senduser['username'] . '">' . $senduser['username'] . '</a> </b> <a href="https://magnet-games.com/viewcomment.php?postid=' . $maincomment['com_id'] . '">#' . $maincomment['com_id'] . '</a> <b> - <span title="UTC - DD/MM/YYYY">(' . TimeToString($date['hours'], $date['minutes']) . ' - ' . $date['mday']. '/' . $date['mon']. '/' . $date['year'] . ')</span>'.
								' - In reply to <a href="https://magnet-games.com/profile.php?username=' . $receiveuser['username'] . '">' . $receiveuser['username'] . '</a>'.
								' <a href="https://magnet-games.com/viewcomment.php?postid=' . $sourcecomment['com_id'] . '"></b>#' . $sourcecomment['com_id'] . '</a>'.
							'</center>'.
						'</div>'.
						'<div style="padding:6px;background: #99DDDD;border-bottom: 1px solid #009999;overflow: auto;">'.
							'<b>' . $receiveuser['username'] . ' said:</b><br>'.
							'<div style="padding:6px;max-height: 24em;overflow-y: auto;background: #CCFFFF;border: 1px solid #009999;">'.
								'<pre style="white-space: pre-wrap;margin: auto;">' . $sourcecomment['com_content'] . '</pre>'.
							'</div>'.
						'</div>'.
						'<div style="padding:6px;max-height: 24em;overflow-y: auto;">'.
							'<pre style="white-space: pre-wrap;margin: auto;">' . $maincomment['com_content'] . '</pre>'.
						'</div>'.
						'<div style="padding:4px;background: #88CCCC;border-top: 1px solid #008888;">'.
							'<b><center><a href="https://magnet-games.com/viewcomment.php?postid=' . $maincomment['com_id'] . '">View This Comment</a></center></b>'.
						'</div>'.
					'</div>'.
				'</div>'.
				'<center>'.
					'You are receiving this email because you have <b>Comment Email Notifications</b> enabled.<br>'.
					'If you do not want notification emails anymore, you can disable them in your <b><a href="https://magnet-games.com/profile.php">User Preferences</a></b>'.
				'</center>';
				
				SendEmail($receiveuser['useremail'], '"' . $senduser['username'] . '" has replied to your comment!', $content);
			}
		}
	}
	
	//Notify when comments are on submissions!
	function SendEmail_Sub($postid)
	{
		$maincomment = GetCommentFromPostID($postid);
		$sourcesubmission = GetSubmissionFromPostID($maincomment['com_subid']);
		$receiveuser = GetUserFromID($sourcesubmission['sub_userid']);
		
		//User has email notifications enabled, isn't banned, and isn't a cherry submission
		if($receiveuser['usernotify'] && $receiveuser['isbanned'] == false && $sourcesubmission['sub_isipfs'] < 2)
		{
			$senduser = GetUserFromID($maincomment['com_userid']);
			//Make sure not to send emails if you're replying to yourself
			if($senduser['userid'] <> $receiveuser['userid'])
			{
				$date = getdate($maincomment['com_date']);
				$subdate = getdate($sourcesubmission['sub_date']);
				$content = '<center><h1 style="padding: 0;margin: 0;"><a href="https://magnet-games.com/profile.php?username=' . $senduser['username'] . '">' . $senduser['username'] . '</a> has commented on your submission!</h1></center><hr>';
				
				//IPFS Themes
				if($sourcesubmission['sub_isipfs'])
				{
					$content .= '<div style="margin-top: auto;margin-left: auto;margin-right: auto;margin-bottom: 0.5em;background: #FFFFFF;width: 100%;border: 0.25em outset #C76DFF;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;">'.
						'<div>'.
							'<div style="background: #E5BAFF;font-size: 1.5em;font-weight: bold;overflow: auto;">'.
								'<center>'.
									'<a href="https://magnet-games.com/index.php?pageid=605"><img src="https://magnet-games.com/cssimg/ipfs.png" title="IPFS Hash"></a> '.
									'<a href="https://ipfs.io/ipfs/' . $sourcesubmission['sub_link'] . '">' . $sourcesubmission['sub_title'] . '</a>'.
								'</center>'.
							'</div>'.
							'<div style="padding:6px;background: #F4E5FF;border-top: 1px solid #8549AB;border-bottom: 1px solid #8549AB;color: #8549AB;overflow: auto;">'.
								'<center>'.
									'<b>' . $sourcesubmission['sub_link'] . '</b>'.
								'</center>'.
							'</div>'.
							'<div style="padding:6px;background: #E5BAFF;border-bottom: 1px solid #8549AB;overflow: auto;">'.
								'<center>'.
									RenderBadges($receiveuser, "https://magnet-games.com") . '<b><a href="https://magnet-games.com/profile.php?username=' . $receiveuser['username'] . '">' . $receiveuser['username'] . '</a></b> '.
									'<a href="https://magnet-games.com/viewsubmission.php?postid=' . $sourcesubmission['sub_id'] . '">#' . $sourcesubmission['sub_id'] . '</a>'.
									'<b> - <span title="UTC - DD/MM/YYYY">(' . TimeToString($subdate['hours'], $subdate['minutes']) . ' - ' . $subdate['mday']. '/' . $subdate['mon']. '/' . $subdate['year'] . ')</span></b>'.
								'</center>'.
							'</div>'.
							'<div style="padding:6px;max-height: 32em;overflow-y: auto;">'.
								'<pre style="white-space: pre-wrap;margin: auto;">' . $sourcesubmission['sub_content'] . '</pre>'.
							'</div>'.
						'</div>'.
						'<div style="padding:4px;background: #D4A9EE;border-top: 1px solid #8549AB;">'.
							'<b><center><a href="https://magnet-games.com/viewsubmission.php?postid=' . $sourcesubmission['sub_id'] . '">View Your Submission</a></center></b>'.
						'</div>'.
					'</div>';
				}else //Magnet Link Themes
				{
					$content .= '<div style="margin-top: auto;margin-left: auto;margin-right: auto;margin-bottom: 0.5em;background: #FFFFFF;width: 100%;border: 0.25em outset #FFC000;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box;">'.
						'<div>'.
							'<div style="background: #FFE000;font-size: 1.5em;font-weight: bold;overflow: auto;">'.
								'<center>'.
									'<a href="https://magnet-games.com/index.php?pageid=604"><img src="https://magnet-games.com/cssimg/magnet.png" title="Magnet Link"></a> '.
									'<a href="' . $sourcesubmission['sub_link'] . '">' . $sourcesubmission['sub_title'] . '</a>'.
								'</center>'.
							'</div>'.
							'<div style="padding:6px;background: #FFE000;border-bottom: 1px solid #AB8100;overflow: auto;">'.
								'<center>'.
									RenderBadges($receiveuser, "https://magnet-games.com") . '<b><a href="https://magnet-games.com/profile.php?username=' . $receiveuser['username'] . '">' . $receiveuser['username'] . '</a></b> '.
									'<a href="https://magnet-games.com/viewsubmission.php?postid=' . $sourcesubmission['sub_id'] . '">#' . $sourcesubmission['sub_id'] . '</a>'.
									'<b> - <span title="UTC - DD/MM/YYYY">(' . TimeToString($subdate['hours'], $subdate['minutes']) . ' - ' . $subdate['mday']. '/' . $subdate['mon']. '/' . $subdate['year'] . ')</span></b>'.
								'</center>'.
							'</div>'.
							'<div style="padding:6px;max-height: 32em;overflow-y: auto;">'.
								'<pre style="white-space: pre-wrap;margin: auto;">' . $sourcesubmission['sub_content'] . '</pre>'.
							'</div>'.
						'</div>'.
						'<div style="padding:4px;background: #EED000;border-top: 1px solid #AB8100;">'.
							'<b><center><a href="https://magnet-games.com/viewsubmission.php?postid=' . $sourcesubmission['sub_id'] . '">View Your Submission</a></center></b>'.
						'</div>'.
					'</div>';
				}
				
				//Comment
				$content .= '<div style="margin-top: auto;margin-left: auto;margin-right: auto;margin-bottom: 0.5em;background: #FFFFFF;width: 100%;border: 0.25em outset #00AAAA;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;box-sizing: border-box; ">'.
					'<div>'.
						'<div style="padding:6px;background: #99DDDD;border-bottom: 1px solid #009999;overflow: auto;">'.
							'<center>'.
								RenderBadges($senduser, "https://magnet-games.com") . '<b><a href="https://magnet-games.com/profile.php?username=' . $senduser['username'] . '">' . $senduser['username'] . '</a> </b> <a href="https://magnet-games.com/viewcomment.php?postid=' . $maincomment['com_id'] . '">#' . $maincomment['com_id'] . '</a> <b> - <span title="UTC - DD/MM/YYYY">(' . TimeToString($date['hours'], $date['minutes']) . ' - ' . $date['mday']. '/' . $date['mon']. '/' . $date['year'] . ')</span></b>'.
							'</center>'.
						'</div>'.
						'<div style="padding:6px;max-height: 24em;overflow-y: auto;">'.
							'<pre style="white-space: pre-wrap;margin: auto;">' . $maincomment['com_content'] . '</pre>'.
						'</div>'.
						'<div style="padding:4px;background: #88CCCC;border-top: 1px solid #008888;">'.
							'<b><center><a href="https://magnet-games.com/viewcomment.php?postid=' . $maincomment['com_id'] . '">View This Comment</a></center></b>'.
						'</div>'.
					'</div>'.
				'</div>'.
				'<center>'.
					'You are receiving this email because you have <b>Comment Email Notifications</b> enabled.<br>'.
					'If you do not want notification emails anymore, you can disable them in your <b><a href="https://magnet-games.com/profile.php">User Preferences</a></b>'.
				'</center>';
				
				SendEmail($receiveuser['useremail'], '"' . $senduser['username'] . '" has commented on your submission!', $content);
			}
		}
	}
?>