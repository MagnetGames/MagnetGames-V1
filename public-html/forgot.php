<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/captcha.php';
	include './src/email.php';
	$DEF_Title = 'Forgot Password';
	$DEF_Desc = 'Forgot your password? Recover your MagnetGames account here!';
	$pagecontent .= PageBegin();
	
	$pagetitle = "<center><h1>Forgot your password?</h1>If you're still having account issues, you can email <a href=" . '"mailto:magnetgames2001@gmail.com"><b>magnetgames2001@gmail.com</b></a><br></center><br>';
	function RenderForgotBox($email)
	{
		$pagecontent = '<form class="SSky_Form" action="./forgot.php" method="post">';	
		$pagecontent .= '<b> Email: </b><input type="text" maxlength="255" name="email" value="' . $email . '"><br>';
		$pagecontent .= '<input type="submit" value="Reset my password!">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function RenderNewPasswordBox($userid, $key)
	{
		$pagecontent = '<form class="SSky_Form" action="./forgot.php?userid=' . $userid . '&key=' . $key . '" method="post">';	
		$pagecontent .= '<b> New Password: </b><input type="password" maxlength="32" name="newpassword"><br>';
		$pagecontent .= '<b> Confirm Password: </b><input type="password" maxlength="32" name="confirmpassword"><br>';
		$pagecontent .= '<input type="submit" value="Change my password!">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	
	//Main forget code here
	if(isset($_SESSION['session_userid']))
	{
		$pagecontent .= Error("You're already logged in!");
		$pagecontent .= GenGoBack();
	}else
	{
		if(isset($_POST["email"]))
		{
			$email = htmlspecialchars(substr($_POST["email"], 0, 255));
			if(!ContainsText($email))
			{
				$pagecontent .= $pagetitle;
				$pagecontent .= Error('Please fill in the box!');
				GenLock();
				$pagecontent .= DrawCaptcha();
				$pagecontent .= RenderForgotBox(null);
				$pagecontent .= GenGoBack();
			}elseif($_SESSION['captcha_locked'])
			{
				$pagecontent .= $pagetitle;
				$pagecontent .= Error('Please complete the puzzle!');
				$pagecontent .= DrawCaptcha();
				$pagecontent .= RenderForgotBox($email);
				$pagecontent .= GenGoBack();
			}else
			{
				$pagecontent .= $pagetitle;
				//big bUG, unactivated users under the same email break
				//$user = GetUserFromEmail($email);
				$query = "SELECT * FROM users WHERE useremail='" . $email . "' AND isactivated='1' LIMIT 1";
				$result = mysqli_query($GLOBALS['con'],$query);
				$user = mysqli_fetch_assoc($result);
				if(isset($user))
				{
					if($user["isbanned"])
					{
						$pagecontent .= Error('This user is banned!');
						GenLock();
						$pagecontent .= DrawCaptcha();
						$pagecontent .= RenderForgotBox($email);
						$pagecontent .= GenGoBack();
					}else
					{
						$pagecontent .= Alert("We've sent a password reset request to your email!<br>Be sure to check 'Spam'.");
						GenLock();
						$pagecontent .= GenGoBack();
						$key = md5(mt_rand());
						PasswordResetHash($user["userid"], $key);
						SendEmail($email, 'Password Reset Request', "<center><h1>Reset your password!</h1>Hi " . $user["username"] . "!<br>You're receiving this email because you (or someone) has requested a password reset.<br><b><a href=" . '"https://magnet-games.com/forgot.php?userid=' . $user["userid"] . '&key=' . $key . '">Click this link if you want to reset it.</a></b><br>This link will expire in 15 minutes.</center><br>');
					}
				}else
				{
					$pagecontent .= Error('That email is not in our database!');
					GenLock();
					$pagecontent .= DrawCaptcha();
					$pagecontent .= RenderForgotBox($email);
					$pagecontent .= GenGoBack();
				}
			}
		//Lets get the actual resetting done
		}elseif(isset($_GET["userid"]) && is_numeric($_GET["userid"]) && isset($_GET["key"]))
		{
			$user = GetUserFromID($_GET["userid"]);
			$pagecontent .= '<center><h1>Change your password!</h1></center>';
			if(isset($user) && $user["isactivated"])
			{
				$pagecontent .= '<center>Welcome ' . $user["username"] . '!</center><br>';
				if($_GET["key"] == $user["resetkey"] && $user["resettime"] >= (time() - 900))
				{
					if(isset($_POST["newpassword"]) && isset($_POST["confirmpassword"]))
					{
						if($_POST["newpassword"] == $_POST["confirmpassword"])
						{
							if(strlen($_POST["newpassword"]) >= 6)
							{
								//If it's over 32 characters
								if(strlen($_POST["newpassword"]) > 32)
								{
									$pagecontent .= Error('Password is too long!');
									$pagecontent .= RenderNewPasswordBox($user["userid"], $_GET["key"]);
								}else //Alright it meets the minimum requirements
								{
									//Change the password
									$newpassword = password_hash($_POST["newpassword"], PASSWORD_DEFAULT);
									ChangeUserPassword($user["userid"], $newpassword);
									$_SESSION['session_userpassword'] = $newpassword; //Don't want the session to end for the user who changed his password!
									$pagecontent .= Alert('Password has been changed!');
									$pagecontent .= '<meta http-equiv="Refresh" content="2; url=./login.php">';
									$pagecontent .= '<center><b>Redirecting...</b></center>';
								}
							}else
							{
								$pagecontent .= Error('Password needs to be at least 6 characters!');
								$pagecontent .= RenderNewPasswordBox($user["userid"], $_GET["key"]);
							}
						}else
						{
							$pagecontent .= Error('Passwords do not match!');
							$pagecontent .= RenderNewPasswordBox($user["userid"], $_GET["key"]);
						}
					}else
					{
						$pagecontent .= RenderNewPasswordBox($user["userid"], $_GET["key"]);
					}
				}else
				{
					$pagecontent .= Error('That password reset request is invalid or expired');
					$pagecontent .= GenGoBack();
				}
			}else
			{
				$pagecontent .= Error("User doesn't exist!");
				$pagecontent .= GenGoBack();
			}
		}
		else
		{
			$pagecontent .= $pagetitle;
			$pagecontent .= DrawCaptcha();
			$pagecontent .= RenderForgotBox(null);
			$pagecontent .= GenGoBack();
		}
	}
	//End of page content
	$pagecontent .= PageEnd();
	//Add the header content to the beginning of the page.
	$headercontent = GenMeta($DEF_Title, $DEF_Desc);
	$headercontent .= file_get_contents($DEF_Header);
	$headercontent .= file_get_contents($DEF_Navbar);
	$headercontent .= DrawUserBar();
	//Finalise page
	echo $headercontent . $pagecontent;
?>