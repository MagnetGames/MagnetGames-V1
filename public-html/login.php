<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/captcha.php';
	$DEF_Title = 'Login';
	$DEF_Desc = 'Login to MagnetGames!';
	$pagecontent .= PageBegin();
	//Renders the box the lets you post a submission
	$pagetitle = "<center><h1>Login:</h1>Don't have an account? <a href=" . '"./register.php"><b>Register</b> one here!</a><br><a href="./forgot.php">Forgot your password?</a></center><br>';
	function RenderLoginBox()
	{
		if(isset($_COOKIE['remember_username']))
		{
			$preremember = ' checked';
			$preuser = $_COOKIE['remember_username'];
			$prepassword = null;
			//$prepassword = $_SESSION['remember_password'];
		}else
		{
			$preremember = null;
			$preuser = null;
			$prepassword = null;
		}
		$pagecontent = '<form class="SSky_Form" action="./login.php" method="post">';	
		$pagecontent .= '<b> Username/Email: </b><input type="text" maxlength="64" name="username" value="' . $preuser . '"><br>';
		$pagecontent .= '<b> Password: </b><input type="password" maxlength="500" name="password" value="' . $prepassword . '"><br>';
		$pagecontent .= '<input type="checkbox" name="rememberme"' . $preremember . '><label for="rememberme"><b title="Warning! While logged in this keeps your passphrase as a cookie! Don\'t use this option on a public machine.">Remember me?</b></label>';
		$pagecontent .= '<input type="submit" value="Login!">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function BadLogin($pagetitle)
	{
		$pagecontent = $pagetitle;
		$pagecontent .= Error('Bad username or password!');
		GenLock();
		$IPAddress = GetIP();
		AddIPLoginAttempt($IPAddress);
		$IPAttempts = GetAttemptsFromIP($IPAddress);
		if($IPAttempts["loginattempts"] >= 5)
		{
			$pagecontent .= DrawCaptcha();
		}
		$pagecontent .= RenderLoginBox();
		$pagecontent .= GenGoBack();
		return $pagecontent;
	}
	if(isset($_SESSION['session_userid']))
	{
		$pagecontent .= Alert('Successfully logged in!');
		$pagecontent .= '<meta http-equiv="Refresh" content="2; url=./' . $GLOBALS['lastpage'] . '">';
		$pagecontent .= '<center><b>Redirecting...</b></center>';
	}else
	{
		$IPAddress = GetIP();
		$IPAttempts = GetAttemptsFromIP($IPAddress);
		if($IPAttempts["loginattempts"] >= 10)
		{
			$pagecontent .= Error("Sorry, you've failed to login more than 10 times.<br>Please come back in 30 minutes.");
			$pagecontent .= GenGoBack();
		}elseif($IPAttempts["isbanned"])
		{
			$pagecontent .= Error("Your IP Address is banned!");
			$pagecontent .= GenGoBack();
		}else
		{
			if(isset($_POST["username"]) && isset($_POST["password"]))
			{
				if(ContainsText($_POST["username"]) && ContainsText($_POST["password"]))
				{
					$username = htmlspecialchars(substr($_POST["username"], 0, 255));
					$password = htmlspecialchars(substr($_POST["password"], 0, 255));
					$user = GetUserFromUsername($username);
					//Hmm, maybe he used his email to login?
					if(!isset($user))
					{
						$user = GetUserFromEmail($username);
					}
					if(isset($user))
					{
						//pls don't log admins IPs i beg u
						if(!$user["isadmin"])
						{
							SetLastUserIP($user["userid"]);
						}
						if(password_verify($password, $user["password"]))
						{
							//Don't let banned people in of course
							if($user["isbanned"])
							{
								$pagecontent .= $pagetitle;
								$pagecontent .= Error('This user is banned!');
								$pagecontent .= RenderLoginBox();
								$pagecontent .= GenGoBack();
							//Or non-activated members
							}elseif(!$user["isactivated"])
							{
								$pagecontent .= $pagetitle;
								$pagecontent .= Error("This user isn't activated!");
								$pagecontent .= RenderLoginBox();
								$pagecontent .= GenGoBack();
							}else
							{
								if($IPAttempts["loginattempts"] >= 5 && $_SESSION['captcha_locked'])
								{
									$pagecontent .= $pagetitle;
									$pagecontent .= Error('Please complete the puzzle!');
									GenLock();
									$pagecontent .= DrawCaptcha();
									$pagecontent .= RenderLoginBox();
									$pagecontent .= GenGoBack();
								}else
								{
									if(isset($_POST["rememberme"]) && $_POST["rememberme"])
									{
										//Legacy, doesn't work well
										//$_SESSION['remember_username'] = $username;
										//$_SESSION['remember_password'] = $password;
										
										//Stop fucking logging me out you bITCH
										setcookie('remember_username', $username, time() + 15552000, "/"); // 15552000 = 6 months
										setcookie('remember_userkey', base64_encode($password), time() + 15552000, "/"); // 15552000 = 6 months
										
									}else
									{
										//$_SESSION['remember_username'] = null;
										//$_SESSION['remember_password'] = null;
										setcookie('remember_username', null, time() - 3600, "/");
										setcookie('remember_userkey', null, time() - 3600, "/");
										
									}
									$_SESSION['session_userid'] = $user["userid"];
									$_SESSION['session_userpassword'] = $user["password"];
									$pagecontent .= Alert('Successfully logged in!');
									$pagecontent .= '<meta http-equiv="Refresh" content="2; url=./' . $GLOBALS['lastpage'] . '">';
									$pagecontent .= '<center><b>Redirecting...</b></center>';
									GenLock();
								}
							}
						}else
						{
							$pagecontent .= BadLogin($pagetitle);
						}
					}else
					{
						$pagecontent .= BadLogin($pagetitle);
					}
				}else
				{
					$pagecontent .= BadLogin($pagetitle);
				}
			}else
			{
				$pagecontent .= $pagetitle;
				if(isset($_GET["showdialog"]) && $_GET["showdialog"] == 1)
				{
					$pagecontent .= Error('You need to be logged in to do that!');
				}
				if($IPAttempts["loginattempts"] >= 5)
				{
					$pagecontent .= DrawCaptcha();
				}
				$pagecontent .= RenderLoginBox();
				$pagecontent .= GenGoBack();
			}
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