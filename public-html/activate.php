<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/captcha.php';
	include './src/email.php';
	$DEF_Title = 'Account Activation';
	$DEF_Desc = 'Activate an account created on MagnetGames!';
	//Renders the box the lets you post a submission
	$pagetitle = "<center><h1>Account Activation</h1>If you're having activation issues, you can email <a href=" . '"mailto:magnetgames2001@gmail.com"><b>magnetgames2001@gmail.com</b></a><br></center><hr>';
	
	$pagecontent .= PageBegin();
	$pagecontent .= $pagetitle;
	if(isset($_SESSION['session_userid']))
	{
		$pagecontent .= Error("You're already logged in!");
		$pagecontent .= GenGoBack();
	}else
	{
		//Lets get the actual activation done
		if(isset($_GET["userid"]) && is_numeric($_GET["userid"]) && isset($_GET["key"]))
		{
			$user = GetUserFromID($_GET["userid"]);
			if(isset($user) && !$user["isactivated"])
			{
				//15 minutes
				if($_GET["key"] == $user["resetkey"] && $user["timecreated"] >= (time() - 900))
				{
					if(IsEmailActivated($user['useremail']) > 0)
					{
						$pagecontent .= Error('That email was just activated recently... You will have to re-register again.');
						$pagecontent .= GenGoBack();
					}else
					{
						$pagecontent .= Alert('<b>Welcome ' . $user["username"] . '!</b><br>Your account is now activated!');
						ActivateUser($user["userid"]);
						$_SESSION['session_userid'] = $user["userid"];
						$_SESSION['session_userpassword'] = $user["password"];
						//Redirect
						$pagecontent .= '<center><b>Redirecting...</b></center>';
						$pagecontent .= '<meta http-equiv="Refresh" content="4; url=./' . $GLOBALS['lastpage'] . '">';
					}
				}else
				{
					$pagecontent .= Error('That activation request is invalid or expired! Please re-register again...');
					$pagecontent .= GenGoBack();
				}
			}else
			{
				$pagecontent .= Error("User doesn't exist or account is already activated!");
				$pagecontent .= GenGoBack();
			}
		}else
		{
			$pagecontent .= Error("Missing page arguments!");
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