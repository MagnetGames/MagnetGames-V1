<?php 
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	//include './src/gravatar.php';
	
	function RenderPreferences($user)
	{
		$pagecontent = '<form class="SSky_Form" action="./profile.php" method="post">';	
		$pagecontent .= 'Fields marked with a (*) only need to be filled for when changing passwords.<br>';
		$pagecontent .= '<b> Old Password(*): </b><input type="password" maxlength="32" name="oldpassword"><br>';
		$pagecontent .= '<b> New Password(*): </b><input type="password" maxlength="32" name="newpassword"><br>';
		$pagecontent .= '<b> Confirm New Password(*): </b><input type="password" maxlength="32" name="confirmpassword"><br>';
		if($user['usernotify'])
		{
			$pagecontent .= '<input type="checkbox" name="notifyemail" checked><label for="notifyemail"><b>Enable Comment Email Notifications?</b></label>';
		}else
		{
			$pagecontent .= '<input type="checkbox" name="notifyemail"><label for="notifyemail"><b>Enable Comment Email Notifications?</b></label>';
		}
		$pagecontent .= '<input type="submit" value="Edit Preferences!">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	

	if(isset($_GET['username']))
	{
		$getusername = substr($_GET['username'], 0, 16);
		//Does the username exist?
		if(GenericCount("users WHERE username='" . PrepSQL($getusername) . "'") > 0)
		{
			$user = GetUserFromUsername($getusername);
		}else //Return anonymous
		{
			$user = GetUserFromID(0);
		}
	}else //No arguments
	{
		//Are you logged in?
		if(isset($_SESSION['session_userid']))
		{
			$user = GetUserFromID($_SESSION['session_userid']);
		}else //Return anonymous as guest
		{
			$user = GetUserFromID(0);
		}
	}
	$regdate = getdate($user['timecreated']);
	//Disabled, buggy with 1970 and privacy risk
	//$postdate = getdate($user['lastposted']);
	$DEF_Title = "Viewing " . $user['username'] . "'s profile.";
	//Main content is here on the page
	$pagecontent .= PageBegin();
		//Check if the user is YOU first
		if(isset($_POST["oldpassword"]) && isset($_POST["newpassword"]) && isset($_POST["confirmpassword"]) && isset($_SESSION['session_userid']) && $_SESSION['session_userid'] == $user['userid'])
		{
			//Are you logged in?
			if(isset($_SESSION['session_userid']))
			{
				//Strip the passwords
				$opassword = substr($_POST["oldpassword"], 0, 32); 
				$npassword = substr($_POST["newpassword"], 0, 32); 
				$cpassword = substr($_POST["confirmpassword"], 0, 32); 
				$newpassword = password_hash($npassword, PASSWORD_DEFAULT);
				
				//Passwords contain text
				if(ContainsText($opassword) && ContainsText($npassword) && ContainsText($cpassword))
				{			
					//Password matches old password
					if(password_verify($opassword, $_SESSION['session_userpassword']))
					{
						//Confirm password etc match
						if($npassword == $cpassword)
						{
							$query = "UPDATE users SET password='" . PrepSQL($newpassword) . "', usernotify='" . isset($_POST["notifyemail"]) . "' WHERE userid='" . $_SESSION['session_userid'] . "'";		
							$result = mysqli_query($GLOBALS['con'],$query);
							$_SESSION['session_userpassword'] = $newpassword; //Don't want the session to end for the user who changed his password!
							$pagecontent .= Alert("Password and email notification settings have been changed!");
						}else
						{
							$pagecontent .= Error("Passwords do not match each other!");
						}
					}else
					{
						$pagecontent .= Error("Password doesn't match your current password!");
					}
					
				}elseif(!ContainsText($opassword) && !ContainsText($npassword) && !ContainsText($cpassword))
				{
					//Change notifications only
					$query = "UPDATE users SET usernotify='" . isset($_POST["notifyemail"]) . "' WHERE userid='" . $_SESSION['session_userid'] . "'";		
					$result = mysqli_query($GLOBALS['con'],$query);
					$pagecontent .= Alert("Email notification settings have been changed!");
				}else //Something isn't filled
				{
					$pagecontent .= Error("Missing fields!");
				}
			}else
			{
				$pagecontent .= Error('You need to be logged in to change user preferences!');
			}
		}
		
		$pagecontent .= '<center>';
		$pagecontent .= get_gravatar($user['useremail'], 128);
		if($user['isbanned'])
		{
			$pagecontent .= '<br><b><i><span style="color: #FF0000;">Banned</span></i></b>';
		}
		$badges = RenderBadges($user);
		$pagecontent .= '<h1>' . $user['username'] . '</h1>';
		$pagecontent .= '<b>Date Registered</b>: ' . '<span title="UTC - DD/MM/YYYY">' . TimeToString($regdate['hours'], $regdate['minutes']) . ' - ' . $regdate['mday']. '/' . $regdate['mon']. '/' . $regdate['year'] . '</span>';
		//Disabled, buggy with 1970 and privacy risk
		//$pagecontent .= '<br><b>Last User Post</b>: ' . '<span title="UTC - DD/MM/YYYY">' . TimeToString($postdate['hours'], $postdate['minutes']) . ' - ' . $postdate['mday']. '/' . $postdate['mon']. '/' . $postdate['year'] . '</span>';
		$pagecontent .= '<br>Number of <b>Comments</b>: ' . GenericCount("comments WHERE com_userid='" . $user['userid'] . "'");
		$pagecontent .= '<br>Number of <b>Magnet-Link Submissions</b>: ' . GenericCount("submissions WHERE sub_userid='" . $user['userid'] . "' AND sub_isipfs='0'");
		$pagecontent .= '<br>Number of <b>IPFS Submissions</b>: ' . GenericCount("submissions WHERE sub_userid='" . $user['userid'] . "' AND sub_isipfs='1'");
		//Makes my job easier
		if(IsUserAdmin($_SESSION['session_userid']))
		{
			$pagecontent .= '<br><b>USERID</b>: ' . $user['userid'];
		}
		//Woah! Badges!
		if(isset($badges))
		{
			$pagecontent .= '<br><b>Badges</b>: ' . $badges;
		}
		$pagecontent .= '<hr></center>';
		$DEF_Desc = $user['username'] . " registered on '" . TimeToString($regdate['hours'], $regdate['minutes']) . ' - ' . $regdate['mday']. '/' . $regdate['mon']. '/' . $regdate['year'] . "'.";
		
		//IF you're viewing your own profile...
		if(isset($_SESSION['session_userid']) && $_SESSION['session_userid'] == $user['userid'])
		{
			$pagecontent .= '<center><h1>User Preferences</h1>';
			$pagecontent .= 'Registered Email: <a href="mailto:' . $user['useremail'] . '">' . $user['useremail'] . '</a>';
			$pagecontent .= '<br>Protip: For custom avatars, they\'re loaded from <b><a href="https://en.gravatar.com">Gravatar</a></b>.</center><br>';
			$pagecontent .= RenderPreferences($user);
		}
		$pagecontent .= '</center>';
		$pagecontent .= GenGoBack();
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