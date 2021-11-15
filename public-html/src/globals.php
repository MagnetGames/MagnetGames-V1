<?php
	$DEF_Title = null;
	$DEF_Desc = 'MagnetGames is a metadata sharing gaming website dedicated for Retro Gamers around the globe!';
	$DEF_Header = './src/pages/header.html';
	$DEF_Navbar = './src/pages/navbar.html';
	$pagecontent = null; //Main Page Content
	$GLOBALS['con'] = mysqli_connect("localhost","username","password","databasename");
	date_default_timezone_set('UTC');
	//Resets the expiration time and logs out cookie hackers
	function CheckSession()
	{
		session_set_cookie_params(15552000); //6 Months
		session_start();

		//For redirect pages
		if(isset($_COOKIE['lastpage'])) //Extend the expiry time
		{
			setcookie('lastpage', $_COOKIE['lastpage'], time() + 86400, "/"); // 86400 = 1 day
			$GLOBALS['lastpage'] = $_COOKIE['lastpage'];
		}else //Set the default to index
		{
			$GLOBALS['lastpage'] = 'index.php';
			setcookie('lastpage', $GLOBALS['lastpage'], time() + 86400, "/"); // 86400 = 1 day
		}
		//So logged in?
		if(isset($_SESSION['session_userid']))
		{
			$user = GetUserFromID($_SESSION['session_userid']);
			$IPAddress = GetIP();
			$IPAttempts = GetAttemptsFromIP($IPAddress);
			//Is the IP or user marked banned?
			if($user["password"] != $_SESSION['session_userpassword'] || $user["isbanned"] == true || $user["isactivated"] == false  || $IPAttempts["isbanned"] == true)
			{
				//Logout, add session expired
				$_SESSION['session_userid'] = null;
				$_SESSION['session_userpassword'] = null;
				$_SESSION['session_expired'] = true;
			}
		//Backup incase shit logs you out
		}elseif(isset($_COOKIE['remember_username']) && isset($_COOKIE['remember_userkey']))
		{
			$user = GetUserFromUsername($_COOKIE['remember_username']);
			$IPAddress = GetIP();
			$IPAttempts = GetAttemptsFromIP($IPAddress);
			if(password_verify(base64_decode($_COOKIE['remember_userkey']), $user["password"]) && $user["isbanned"] == false && $user["isactivated"] == true && $IPAttempts["isbanned"] == false)
			{
				//WEW recovered session
				$_SESSION['session_userid'] = $user["userid"];
				$_SESSION['session_userpassword'] = $user["password"];
				$_SESSION['session_expired'] = false;
				
				//Extend the cookie length
				setcookie('remember_username', $_COOKIE['remember_username'], time() + 15552000, "/"); // 15552000 = 6 months
				setcookie('remember_userkey', $_COOKIE['remember_userkey'], time() + 15552000, "/"); // 15552000 = 6 months
			}else //Password does not match, is banned, or isn't activated. But hey the cookie for it exists
			{
				//Logout, add session expired
				$_SESSION['session_userid'] = null;
				$_SESSION['session_userpassword'] = null;
				$_SESSION['session_expired'] = true;
				setcookie('remember_username', null, time() - 3600, "/");
				setcookie('remember_userkey', null, time() - 3600, "/");
			}
		}
		//Visitor Count
		if(isset($_SESSION['session_visited']))
		{
			//Visitor sessions count for a day
			if($_SESSION['session_visited'] < (time() - 86400)) //1 day
			{
				//Add a count
				$_SESSION['session_visited'] = time();
				AddVisit();
			}		
		}else //Visited before the visited variable existed
		{
			//Add a count
			$_SESSION['session_visited'] = time();
			AddVisit();
		}
	}
	//Renders an alert
	function Alert($alert_msg)
	{
		$pagecontent = '<div class="SLime_Comment">';
			$pagecontent .= '<div class="SLime_CommentTitle">';
			$pagecontent .= '<center>';
			$pagecontent .= $alert_msg;
			$pagecontent .= '</center></div>';
		$pagecontent .= '</div>';
		return $pagecontent;
	}
	//jesus christ if this leaks, im seriously so fucked holy shit PLEASE don't leak
	function CherryURL()
	{
		return 'http://getyourownCDN.com'; //Changeed for 2021 Source Release
	}
	//Renders an ERROR
	function Error($error_msg, $error_ttl = 'ERROR!')
	{
		$pagecontent = '<div class="SRed_Comment">';
			$pagecontent .= '<div class="SRed_CommentTitle">';
			$pagecontent .= '<center><b>' . $error_ttl . '</b><br>';
			$pagecontent .= $error_msg;
			$pagecontent .= '</center></div>';
		$pagecontent .= '</div>';
		return $pagecontent;
	}
	function RenderBadges($user, $preurl = '.')
	{
		$badgeshtml = null;
		if($user['isadmin']) //Admin badge
		{
			$badgeshtml .= '<img src="' . $preurl . '/cssimg/badges/admin.gif" title="This user is an Admin!"> ';
		}
		//User specific badges
		switch ($user['userid']) 
		{
			case 0: //Anonymous user
				$badgeshtml .= '<img src="' . $preurl . '/cssimg/badges/anon.gif" title="Shared Anonymous Account"> ';
				break;
			//These guys here? good bois, they deserve a star
			case 21: //T5P
			case 36: //Redump
			case 38: //No-intro
			case 40: //TopHatFrenchman			
				$badgeshtml .= '<img src="' . $preurl . '/cssimg/badges/contrib.gif" title="This user is a Top Contributor!"> ';
				break;
		}
		//if(true) //Placeholder, for when we get OUT of beta.
		if($user['timecreated'] < 1551951607) //7:40PM 7th march 2019
		{
			$badgeshtml .= '<img src="' . $preurl . '/cssimg/badges/beta.gif" title="This user was here for the MagnetGames Beta Party!"> ';
		}
		return $badgeshtml;
	}
	//Allows - . _ A-Z a-z 0-9
	function IsValidUsername($str) 
	{
		//each array entry is a special char thats allowed
		//besides the ones from ctype_alnum
		$allowed = array(".", "-", "_");
		if(ctype_alnum(str_replace($allowed, '', $str))) 
		{
			return true;
		}else 
		{
			return false;
		}
	}
	function CheckPage()
	{
		if(isset($_GET["pageid"]) && is_numeric($_GET["pageid"]))
		{	
			$GLOBALS['pageid'] = $_GET["pageid"];
		}else
		{
			$GLOBALS['pageid'] = 0;
		}
		if(!file_exists('src/pages/page_' . $GLOBALS['pageid'] . '.html'))
		{
			$GLOBALS['pageid'] = 404;
		}
	}
	//Gets the full username from an ID
	function GetUsernameFromID($user_id)
	{
		$query = "SELECT username FROM users WHERE userid='" . PrepSQL($user_id) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['username'];
	}
	function GetUserFromID($user_id)
	{
		$query = "SELECT * FROM users WHERE userid='" . PrepSQL($user_id) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	function GetUserFromUsername($username)
	{
		$query = "SELECT * FROM users WHERE username='" . PrepSQL($username) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	function GetUserFromEmail($useremail)
	{
		$query = "SELECT * FROM users WHERE useremail='" . PrepSQL($useremail) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	//IsAdmin
	function IsUserAdmin($user_id)
	{
		$query = "SELECT isadmin FROM users WHERE userid='" . PrepSQL($user_id) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['isadmin'];
	}
	//Usercount
	function UserCount()
	{
		$query = "SELECT COUNT(*) FROM users LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	//Check if an email is in use
	function IsEmailActivated($useremail)
	{
		$query = "SELECT COUNT(*) FROM users WHERE useremail='" . PrepSQL($useremail) . "' AND isactivated='1' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	//Activates a user
	//Edits a user's lastpost time 
	function ActivateUser($userid)
	{
		$query = "UPDATE users SET isactivated='1' WHERE userid='" . PrepSQL($userid) . "' LIMIT 1";		
		$result = mysqli_query($GLOBALS['con'],$query);
	}
	//Just basic on the fly comment headers etc
	function PageBegin()
	{
		$pagecontent = '<script src="./cssimg/common.js"></script>';
		$pagecontent .= '<div class="MainPage"><div class="MainContent">';
		//Either banned or password was changed
		if(isset($_SESSION['session_expired']) && $_SESSION['session_expired'] == true)
		{
			$pagecontent .= Error("Please login again.", "Session Expired");
			$_SESSION['session_expired'] = null;
		}
		return $pagecontent;
	}
	function PageEnd()
	{
		$pagecontent = '</div></div>';
		$pagecontent .= file_get_contents("./src/pages/footer.html");	
		return $pagecontent;
		$GLOBALS['con']->close(); //End MYSql
	}
	function DrawUserBar()
	{
		$pagecontent = '<div class="UserBar">';
		if(isset($_SESSION['session_userid']))
		{
			$tmp_user = GetUserFromID($_SESSION['session_userid']);
			if($tmp_user['isadmin'])
			{
				$suggestcount = GenericCount("suggestions WHERE deleted='0'");
				$reportcount = GenericCount("reports WHERE deleted='0'");
				$pagecontent .= '<span class="UserBarContent"><b><span style="color: #880000;">' . $reportcount . ' Report(s)</span>, <span style="color: #888800;">' . $suggestcount . ' Suggestion(s)</span> - <i>Welcome <a href="./profile.php" title="View your profile!">' . GetUsernameFromID($_SESSION['session_userid']) . '!</a></i> - <a href="./acp.php" title="Admin Control Panel">ACP</a> - <a href="./logout.php" title="Log out of MagnetGames!">Logout</a></b></span>';	
			}else
			{
				$pagecontent .= '<span class="UserBarContent"><b><i>Welcome <a href="./profile.php" title="View your profile!">' . GetUsernameFromID($_SESSION['session_userid']) . '!</a></i> - <a href="./logout.php" title="Log out of MagnetGames!">Logout</a></b></span>';
			}
			
		}else
		{
			$pagecontent .= '<span class="UserBarContent"><b><i>Welcome Guest!</i> - <a href="./login.php" title="Login to MagnetGames!">Login</a> - <a href="./register.php" title="Create an account!">Register</a></b></span>';
		}
		$pagecontent .= '</div>';
		return $pagecontent;
	}
	//This is probably gonna be shit, but lets see
	function DrawAdvertisements($header = null, $footer = null)
	{
		//Override, in case we're gonna fuck ads off later down the line
		if(false)
		{
			//Advertisements
			$pagecontent = $header;
			$pagecontent .= '<center><div style="background: #008888;border: 1px dotted #00FFFF;width: 500px;">';
			$pagecontent .= '<b style="color: #FFFF00;text-shadow:1px 1px #000000;">Advertisement:</b><br>';
			//$pagecontent .= '<img src="./cssimg/adprototype.png" style="border: 1px dotted #FFFF00;">';	
			$pagecontent .= '</div></center>';
			$pagecontent .= $footer;
			return $pagecontent;
		}else
		{
			return null;
		}
	}
	function DateToString($date)
	{
		return $date['mday'] . '/' . $date['mon'] . '/' . $date['year'];
	}
	//HTML metadata tags
	function GenMeta($title, $description)
	{
		if(isset($title))
		{
			$title = strip_tags($title) . ' - MagnetGames';
		}else
		{
			$title = 'MagnetGames';
		}
		
		//Some SEO bullshit for the home page
		//JESUS CHRIST I MADE EVERY PAGE THINK IT WAS THE HOME PAGE THIS WHOLE TIME FOR MAGNET GAMES WTF IM FUCKING RETARDed
		if(isset($GLOBALS['pageid']))
		{
			//game ids
			if(isset($GLOBALS['gameid']))
			{
				$canonical = '<link rel="canonical" href="https://magnet-games.com/index.php?gameid=' . $GLOBALS['gameid'] . '&pttl=' . GenTitle(GetGameNameFromID($GLOBALS['gameid'])) . '">';
			}else //Not a game id
			{
				//Home page
				if($GLOBALS['pageid'] == 0)
				{
					$canonical = '<link rel="canonical" href="https://magnet-games.com">';
				}else //Get the page title and shit
				{
					$canonical = '<link rel="canonical" href="https://magnet-games.com/index.php?pageid=' . $GLOBALS['pageid'] . '&pttl=' . GenTitle(GenTitleForPageID($GLOBALS['pageid'])) . '">';
				}				
			}			
		}else
		{
			$canonical = null; 
		}
		$description = strip_tags($description);
		if(IsMobile())
		//if(true)
		{
			$stylesheet = './cssimg/style_mob.css';
			$viewport = '<meta name="viewport" content="width=640">';
		}else
		{
			$stylesheet = './cssimg/style.css';
			$viewport = null;
		}
		return '<!DOCTYPE html>'.
				'<html lang="en">'.
					'<head>'.
						'<meta charset="UTF-8">'.
						'<title>'. $title .'</title>'.
						'<meta name="description" content="' . $description . '">'.
						'<meta name="keywords" content="Magnet Games,MagnetGames,ROM,ROMS,ISO,IMAGES,RETRO GAMING,OLD SCHOOL,GAMES,GAMERS,SHARING,COMMUNITY">'.
						'<meta name="author" content="Magnet Games">'.
						'<meta name="twitter:title" content="' . $title . '">'.
						'<meta name="twitter:description" content="' . $description . '">'.
						'<meta name="twitter:image" content="http://magnet-games.com/cssimg/twitterbg.jpg">'.
						'<meta property="og:type" content="website">'.
						'<meta property="og:url" content="https://magnet-games.com">'.
						'<meta property="og:site_name" content="Magnet Games">'.
						'<meta property="og:title" content="' . $title . '">'.
						'<meta property="og:description" content="' . $description . '">'.
						'<meta property="og:image" content="http://magnet-games.com/cssimg/socialbg.jpg">'.
						'<meta property="og:locale" content="en_GB">'.
						'<link rel="icon" type="image/png" href="./favicon.png">'.
						'<link rel="stylesheet" type="text/css" href="' . $stylesheet . '">'.
						$viewport.
						$canonical.						
					'</head>';
	}
	//Gen Page Title - To make URLS look nicer
	function GenTitle($title)
	{
		$newtitle = strip_tags($title);
		$newtitle = strtolower($newtitle);
		$newtitle = str_replace(' ', '-', $newtitle);
		$newtitle = str_replace('\\', '_', $newtitle);
		$newtitle = str_replace('/', '_', $newtitle);
		$newtitle = preg_replace('/[^a-z0-9\-_]/', '', $newtitle); //Only a-z 0-9 and dashes
		$newtitle = str_replace('-_-', '_', $newtitle);
		$newtitle = str_replace('---', '-', $newtitle);
		$newtitle = str_replace('--', '-', $newtitle);
		return $newtitle;
	}
	//RedirectVar
	function GenRedirect()
	{
		//Check for if you actually have real arguments, otherwise it'll redirect to a broken URL
	//	if(isset($_GET["pageid"]) && is_numeric($_GET["pageid"]))
		if(count($_GET) > 0)
		{
			$GLOBALS['lastpage'] = htmlspecialchars(basename($_SERVER['REQUEST_URI']));
			setcookie('lastpage', $GLOBALS['lastpage'], time() + 86400, "/"); // 86400 = 1 day
		}else
		{
			$GLOBALS['lastpage'] = htmlspecialchars(basename($_SERVER['REQUEST_URI']));
			setcookie('lastpage', 'index.php', time() + 86400, "/"); // 86400 = 1 day
		}
	}
	//Makes a Go Back link using the top function
	function GenGoBack($extendedURL = null)
	{
		return '<center><b><a href="' . htmlspecialchars($GLOBALS['lastpage']) . $extendedURL . '">Go back</a></b></center>';
	}
	//Converts single digits to double
	function GenDoubleDigits($number)
	{
		if($number < 10)
		{
			return '0' . $number;
		}else
		{
			return $number;
		}
	}
	//Formats time correctly
	Function TimeToString($hours, $minutes)
	{
		
		if($hours >= 12)
		{
			if($hours > 12)
			{
				$newhours = $hours - 12;
			}
			else
			{
				$newhours = $hours;
			}
			$endstring = 'PM';
		}else
		{
			$newhours = $hours;
			$endstring = 'AM';
		}
		return GenDoubleDigits($newhours) . ':' . GenDoubleDigits($minutes) . $endstring;
	}

	//Checks if text is just spaces/newlines
	function ContainsText($text)
	{
		if(strlen(preg_replace('/\s+/u', '', $text)) == 0)
		{
			return false;
		}else
		{
			return true;
		}
	}
	//Prevents SQL injection
	function PrepSQL($value)
	{
		return mysqli_real_escape_string($GLOBALS['con'], $value);
	}
	//really SHIT implementation where I can have a comment section on gamelists, submissions, and pages.
	function GetPageType()
	{
		if(isset($GLOBALS['gameid']) && !isset($GLOBALS['subid']))
		{
			return 'gameid';
		}elseif(!isset($GLOBALS['gameid']) && !isset($GLOBALS['subid']))
		{
			return 'pageid';
		}elseif(isset($GLOBALS['subid']))
		{
			return 'subid';
		}
	}
	//Password reset
	function PasswordResetHash($userid, $newhash)
	{
		$query = "UPDATE users SET resetkey='" . $newhash . "', resettime='" . time() . "' WHERE userid='" . PrepSQL($userid) . "'";		
		$result = mysqli_query($GLOBALS['con'],$query);
	}
	function ChangeUserPassword($userid,$password)
	{
		$query = "UPDATE users SET password='" . $password . "', resettime='0' WHERE userid='" . PrepSQL($userid) . "'";		
		$result = mysqli_query($GLOBALS['con'],$query);
	}
	//Statistic stuff
	function GetStat($statname)
	{
		$query = "SELECT * FROM stats WHERE statname='" . $statname . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		return mysqli_fetch_assoc($result);
	}
	function AddVisit()
	{
		$TotalVisitors = GetStat('totaluniquevisits');
		$TotalDayVisitors = GetStat('uniquevisitsday');
		
		//Update Total Unique Visitors
		$query = "UPDATE stats SET statcount='" . ($TotalVisitors['statcount'] + 1) . "', stattime='" . time() . "' WHERE statname='totaluniquevisits'";		
		$result = mysqli_query($GLOBALS['con'],$query);
		
		//If it's been more than a day
		if($TotalDayVisitors['stattime'] < (time() - 86400)) //1 day
		{
			//Reset it to 1
			$query = "UPDATE stats SET statcount='1', stattime='" . time() . "' WHERE statname='uniquevisitsday'";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}else
		{
			//Add 1, don't change the time
			$query = "UPDATE stats SET statcount='" . ($TotalDayVisitors['statcount'] + 1) . "' WHERE statname='uniquevisitsday'";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}
	}
	//Gets the count of anything
	function GenericCount($content)
	{
		$query = "SELECT COUNT(*) FROM " . $content . " LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	
	//SpamBots/IP Address stuff
	function GetIP() //Stolen code I got
	{
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
	 
		return md5($ipaddress); //Return IP addresses as an MD5 hash
	}
	function CheckIP($tmp_ip)
	{
		$query = "SELECT COUNT(*) FROM failedlogins WHERE ipaddress='" . PrepSQL($tmp_ip) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		if($row['COUNT(*)'])
		{
			return true;
		}else
		{
			return false;
		}
	}
	function AddIP($tmp_ip)
	{
		$query = "INSERT INTO failedlogins (ipaddress, loginattempts, captchaattempts, lasttime, lasttimec) VALUES ('" . PrepSQL($tmp_ip) . "', '0', '0', '" . time() . "', '" . time() . "')";
		$result = mysqli_query($GLOBALS['con'],$query);
	}
	function AddIPLoginAttempt($tmp_ip)
	{
		$login = GetAttemptsFromIP($tmp_ip);
		if($login["lasttime"] >= (time() - 1800)) //30 minutes
		{
			$newattempts = $login["loginattempts"] + 1;
		}else
		{
			$newattempts = 1;
		}		
		$query = "UPDATE failedlogins SET loginattempts='" . $newattempts . "', lasttime='" . time() . "' WHERE ipaddress='" . PrepSQL($tmp_ip) . "'";		
		$result = mysqli_query($GLOBALS['con'],$query);
	}
	function AddIPCaptchaAttempt($tmp_ip)
	{
		$login = GetAttemptsFromIP($tmp_ip);
		if($login["lasttimec"] >= (time() - 14400)) //4 hours
		{
			$newattempts = $login["captchaattempts"] + 1;
		}else
		{
			$newattempts = 1;
		}		
		$query = "UPDATE failedlogins SET captchaattempts='" . $newattempts . "', lasttimec='" . time() . "' WHERE ipaddress='" . PrepSQL($tmp_ip) . "'";		
		$result = mysqli_query($GLOBALS['con'],$query);
	}
	function GetAttemptsFromIP($tmp_ip)
	{
		$query = "SELECT * FROM failedlogins WHERE ipaddress='" . PrepSQL($tmp_ip) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		//Doesn't exist? Create a listing
		if(!CheckIP($tmp_ip))
		{
			AddIP($tmp_ip);
			$query = "SELECT * FROM failedlogins WHERE ipaddress='" . PrepSQL($tmp_ip) . "' LIMIT 1";
			$result = mysqli_query($GLOBALS['con'],$query);	
		}
		$row = mysqli_fetch_assoc($result);
		if($row["lasttimec"] < (time() - 14400)) //4 hours
		{
			$row["captchaattempts"] = 0;
		}		
		if($row["lasttime"] < (time() - 1800)) //4 hours
		{
			$row["loginattempts"] = 0;
		}		
		return $row;
	}
	//Changes last login IP
	function SetLastUserIP($userid)
	{
		$query = "UPDATE users SET userip='" . GetIP() ."' WHERE userid='" . PrepSQL($userid) . "' LIMIT 1";		
		$result = mysqli_query($GLOBALS['con'],$query);
	}
	//Stolen from somewhere
	function IsMobile()
	{
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
	//Stolen gravatar code
	function get_gravatar( $email, $s = 80, $class = "SAqua_PAvatar", $d = 'mp', $r = 'x', $img = true, $atts = array()) 
	{
		//$avatarcolour = "#AA4444";
		//$avatarcolour = "#00AAAA";
		//$avatarcolour = "#AA44AA";
		$url = 'https://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img class="' . $class . '" src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}	
	//AUTOEXEC
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	CheckSession();
?>