<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	include './src/email.php';
	include './src/metadata.php';
	$DEF_Title = 'Admin Control Panel';
	$pagecontent .= PageBegin();

	function ACPBox($contentname, $vartext = "Value", $buttext = "Submit", $precontent = null)
	{
		$pagecontent = '<form class="SAqua_Form" action="./acp.php" method="get">';	
		$pagecontent .= '<input type="hidden" name="acp_page" value="' . $_GET['acp_page'] . '"/>';
		$pagecontent .= '<b>' . $vartext . ': </b><br><textarea rows="8" id="text" name="' . $contentname . '">' . $precontent . '</textarea><br>';
		$pagecontent .= '<input type="submit" value="' . $buttext . '">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function ACPBoxUserID($contentname, $vartext = "Value", $buttext = "Submit", $precontent = null)
	{
		$pagecontent = '<form class="SAqua_Form" action="./acp.php?acp_page=' . $_GET['acp_page'] . '&userid=' . $_GET['userid'] . '" method="post">';	
		$pagecontent .= '<b>' . $vartext . ': </b><br><textarea rows="8" id="text" name="' . $contentname . '">' . $precontent . '</textarea><br>';
		$pagecontent .= '<input type="submit" value="' . $buttext . '">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function ACPBoxPostID($contentname, $vartext = "Value", $buttext = "Submit", $precontent = null)
	{
		$pagecontent = '<form class="SAqua_Form" action="./acp.php?acp_page=' . $_GET['acp_page'] . '&postid=' . $_GET['postid'] . '" method="post">';	
		$pagecontent .= '<b>' . $vartext . ': </b><br><textarea rows="8" id="text" name="' . $contentname . '">' . $precontent . '</textarea><br>';
		$pagecontent .= '<input type="submit" value="' . $buttext . '">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function ACPBoxSubID($contentname, $vartext = "Value", $buttext = "Submit", $precontent = null)
	{
		$pagecontent = '<form class="SAqua_Form" action="./acp.php?acp_page=' . $_GET['acp_page'] . '&subid=' . $_GET['subid'] . '" method="post">';	
		$pagecontent .= '<b>' . $vartext . ': </b><br><textarea rows="8" id="text" name="' . $contentname . '">' . $precontent . '</textarea><br>';
		$pagecontent .= '<input type="submit" value="' . $buttext . '">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function ACPBoxGameID($contentname, $vartext = "Value", $buttext = "Submit", $precontent = null)
	{
		$pagecontent = '<form class="SAqua_Form" action="./acp.php?acp_page=' . $_GET['acp_page'] . '&gameid=' . $_GET['gameid'] . '" method="post">';	
		$pagecontent .= '<b>' . $vartext . ': </b><br><textarea rows="8" id="text" name="' . $contentname . '">' . $precontent . '</textarea><br>';
		$pagecontent .= '<input type="submit" value="' . $buttext . '">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function ACPUserIDDetails($user_id)
	{
		$query = "SELECT * FROM users WHERE userid='" . PrepSQL($user_id) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	function ACPComIDDetails($post_id)
	{
		$query = "SELECT * FROM comments WHERE com_id='" . PrepSQL($post_id) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	function ACPSubIDDetails($post_id)
	{
		$query = "SELECT * FROM submissions WHERE sub_id='" . PrepSQL($post_id) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	function ACPGameIDDetails($post_id)
	{
		$query = "SELECT * FROM gamelist WHERE gameid='" . PrepSQL($post_id) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	//Renders the box the lets you suggest a game
	function ACPGameBox($prepageid,$prepageindex, $pretitle, $predate, $precontent, $pregamerating, $pregamecover)
	{
		$pagecontent = '<form class="SSky_Form" action="./acp.php?acp_page=' . $_GET['acp_page'] . '" method="post">';	
		$pagecontent .= '<b> PageID: </b><input type="text" name="pageid" value="' . $prepageid . '"><br>';
		$pagecontent .= '<b> Letter Index: </b><input type="text" name="letterindex" maxlength="1" value="' . $prepageindex . '"><br>';
		$pagecontent .= '<b> Game Title: </b><input type="text" name="gametitle" value="' . $pretitle . '"><br>';
		$pagecontent .= '<b title="E.g. 5th September, 1999"> Game Release Date: </b><input type="text" name="gamedate" value="' . $predate . '"><br>';
		$pagecontent .= '<b> Game Description: </b><br><textarea rows="16" id="desc" name="content">' . $precontent . '</textarea><br>';
		$pagecontent .= '<b> Game Rating: </b><input type="text" name="gamerating" maxlength="3" value="' . $pregamerating . '"><br>';
		$pagecontent .= '<b> Game Cover URL (Optional): </b><input type="text" name="gamecover" maxlength="512" value="' . $pregamecover . '"><br>';
		$pagecontent .= '<input type="submit" value="Add Game">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function ACPSGameBox($prepageid,$prepageindex, $pretitle, $predate, $precontent, $pregamerating, $pregamecover, $userid = null)
	{
		$pagecontent = '<form class="SSky_Form" action="./acp.php?acp_page=' . $_GET['acp_page'] . '&addsuggest=' . $_GET['addsuggest'] . $userid . '" method="post">';	
		$pagecontent .= '<b> PageID: </b><input type="text" name="pageid" value="' . $prepageid . '"><br>';
		$pagecontent .= '<b> Letter Index: </b><input type="text" name="letterindex" maxlength="1" value="' . $prepageindex . '"><br>';
		$pagecontent .= '<b> Game Title: </b><input type="text" name="gametitle" value="' . $pretitle . '"><br>';
		$pagecontent .= '<b title="E.g. 5th September, 1999"> Game Release Date: </b><input type="text" name="gamedate" value="' . $predate . '"><br>';
		$pagecontent .= '<b> Game Description: </b><br><textarea rows="16" id="desc" name="content">' . $precontent . '</textarea><br>';
		$pagecontent .= '<b> Game Rating: </b><input type="text" name="gamerating" maxlength="3" value="' . $pregamerating . '"><br>';
		$pagecontent .= '<b> Game Cover URL (Optional): </b><input type="text" name="gamecover" maxlength="512" value="' . $pregamecover . '"><br>';
		$pagecontent .= '<input type="submit" value="Add Game and Approve">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	//Renders the box the lets you post a submission
	function ACPRenderSubmissionBox($preid, $presubid, $pretitle, $precontent, $prelink)
	{
		$pagecontent = '<form class="SSky_Form" action="./acp.php?acp_page=' . $_GET['acp_page'] . '" method="post">';	
		$pagecontent .= '<b> GameID: </b><input type="text" name="gameid" value="' . $preid . '"><br>';
		$pagecontent .= '<b> Base Submission ID: </b><input type="text" name="basesubid" value="' . $presubid . '"><br>';
		$pagecontent .= '<b> Submission Title (Max 64 chars): </b><input type="text" maxlength="64" name="subtitle" value="' . $pretitle . '"><br>';
		$pagecontent .= '<b> Submission Description (Max 5000 chars): </b><br><textarea rows="16" maxlength="5000" id="desc" name="content">' . $precontent . '</textarea><br>';
		$pagecontent .= '<b> URL: </b><input type="text" name="sublink" value="' . $prelink . '"><br>';
		$pagecontent .= '<input type="submit" value="Post Submission">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function ACPDrawGame($row)
	{
		return Alert('GameID: ' . $row['gameid'] .
		'<br><img src="./cssimg/page_' . $row['pageid'] . '.gif">' .
		'<br>PageID:' . $row['pageid'] .
		'<br><b>Title: </b>' . $row['gametitle'] .
		'<br><b>Date: </b>' . $row['gamedate'] .
		'<br><b>Description: </b><br><pre>' . $row['gamedesc'] . '</pre>');
	}
	function CountSuggestions()
	{
		$query = "SELECT COUNT(*) FROM suggestions WHERE deleted='0'";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function CountSuggestionsDel()
	{
		$query = "SELECT COUNT(*) FROM suggestions WHERE deleted='1'";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function CountReports()
	{
		$query = "SELECT COUNT(*) FROM reports WHERE deleted='0'";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function CountReportsDel()
	{
		$query = "SELECT COUNT(*) FROM reports WHERE deleted='1'";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	
	if(isset($_SESSION['session_userid']) && IsUserAdmin($_SESSION['session_userid']))
	{
		$pagecontent .= '<center><h1><a href="./acp.php">Admin Control Panel</a></h1></center><br>';
		if(isset($_GET['acp_page']))
		{
			//Users
			if($_GET['acp_page'] == 'banuser')
			{
				if(isset($_GET['userid']))
				{
					if(isset($_POST['newvalue']))
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$query = "UPDATE users SET isbanned='" . PrepSQL($_POST['newvalue']) . "' WHERE userid='" . PrepSQL($_GET['userid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed ban status from: ' . $user['isbanned'] . ' to: ' . $_POST['newvalue']);
					}else
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$pagecontent .= Alert('<b>' . $user['username'] . '</b> ban status: ' . $user['isbanned']);
						$pagecontent .= ACPBoxUserID('newvalue', 'new ban status', 'change', $user['isbanned']);
					}				
				}else
				{
					$pagecontent .= '<b>change ban status</b><br>';
					$pagecontent .= ACPBox('userid', 'UserID', 'get ban status');
				}
			}elseif($_GET['acp_page'] == 'changeusername')
			{
				if(isset($_GET['userid']))
				{
					if(isset($_POST['newvalue']))
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$query = "UPDATE users SET username='" . PrepSQL($_POST['newvalue']) . "' WHERE userid='" . PrepSQL($_GET['userid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed username from: ' . $user['username'] . ' to: ' . $_POST['newvalue']);
					}else
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$pagecontent .= Alert('username: ' . $user['username']);
						$pagecontent .= ACPBoxUserID('newvalue', 'new username', 'change', $user['username']);
					}				
				}else
				{
					$pagecontent .= '<b>change username</b><br>';
					$pagecontent .= ACPBox('userid', 'UserID', 'get user');
				}
			}elseif($_GET['acp_page'] == 'changeemail')
			{
				if(isset($_GET['userid']))
				{
					if(isset($_POST['newvalue']))
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$query = "UPDATE users SET useremail='" . PrepSQL($_POST['newvalue']) . "' WHERE userid='" . PrepSQL($_GET['userid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed useremail from: ' . $user['useremail'] . ' to: ' . $_POST['newvalue']);
					}else
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$pagecontent .= Alert('useremail: ' . $user['useremail']);
						$pagecontent .= ACPBoxUserID('newvalue', 'new useremail', 'change', $user['useremail']);
					}				
				}else
				{
					$pagecontent .= '<b>change useremail</b><br>';
					$pagecontent .= ACPBox('userid', 'UserID', 'get user');
				}
			}elseif($_GET['acp_page'] == 'changepassword')
			{
				if(isset($_GET['userid']))
				{
					if(isset($_POST['newvalue']))
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$query = "UPDATE users SET password='" . password_hash(PrepSQL($_POST['newvalue']), PASSWORD_DEFAULT) . "' WHERE userid='" . PrepSQL($_GET['userid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed password to: ' . $_POST['newvalue']);
					}else
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$pagecontent .= Alert('username: ' . $user['username']);
						$pagecontent .= ACPBoxUserID('newvalue', 'new password', 'change');
					}				
				}else
				{
					$pagecontent .= '<b>change username</b><br>';
					$pagecontent .= ACPBox('userid', 'UserID', 'get user');
				}
			}elseif($_GET['acp_page'] == 'changecreationdate')
			{
				if(isset($_GET['userid']))
				{
					if(isset($_POST['newvalue']))
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$query = "UPDATE users SET timecreated='" . PrepSQL($_POST['newvalue']) . "' WHERE userid='" . PrepSQL($_GET['userid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed timecreated from: ' . $user['username'] . ' to: ' . $_POST['newvalue']);
					}else
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$pagecontent .= Alert('username: ' . $user['username'] . ' currenttime: ' . time());
						$pagecontent .= ACPBoxUserID('newvalue', 'new date created', 'change', $user['timecreated']);
					}				
				}else
				{
					$pagecontent .= '<b>change creation date</b><br>';
					$pagecontent .= ACPBox('userid', 'UserID', 'get user');
				}
			}elseif($_GET['acp_page'] == 'changeadmin')
			{
				if(isset($_GET['userid']))
				{
					if(isset($_POST['newvalue']))
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$query = "UPDATE users SET isadmin='" . PrepSQL($_POST['newvalue']) . "' WHERE userid='" . PrepSQL($_GET['userid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed isadmin from: ' . $user['isadmin'] . ' to: ' . $_POST['newvalue']);
					}else
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$pagecontent .= Alert('username: ' . $user['username']);
						$pagecontent .= ACPBoxUserID('newvalue', 'new admin status', 'change', $user['isadmin']);
					}				
				}else
				{
					$pagecontent .= '<b>change admin status</b><br>';
					$pagecontent .= ACPBox('userid', 'UserID', 'get user');
				}
			}elseif($_GET['acp_page'] == 'changeactivation')
			{
				if(isset($_GET['userid']))
				{
					if(isset($_POST['newvalue']))
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$query = "UPDATE users SET isactivated='" . PrepSQL($_POST['newvalue']) . "' WHERE userid='" . PrepSQL($_GET['userid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed isactivated from: ' . $user['isactivated'] . ' to: ' . $_POST['newvalue']);
					}else
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$pagecontent .= Alert('username: ' . $user['username']);
						$pagecontent .= ACPBoxUserID('newvalue', 'new activation status', 'change', $user['isactivated']);
					}				
				}else
				{
					$pagecontent .= '<b>change activated status</b><br>';
					$pagecontent .= ACPBox('userid', 'UserID', 'get user');
				}
			}elseif($_GET['acp_page'] == 'changeip')
			{
				if(isset($_GET['userid']))
				{
					if(isset($_POST['newvalue']))
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$query = "UPDATE users SET userip='" . PrepSQL($_POST['newvalue']) . "' WHERE userid='" . PrepSQL($_GET['userid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed userip from: ' . $user['userip'] . ' to: ' . $_POST['newvalue']);
					}else
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$pagecontent .= Alert('username: ' . $user['username']);
						$pagecontent .= ACPBoxUserID('newvalue', 'new last userip', 'change', $user['userip']);
					}				
				}else
				{
					$pagecontent .= '<b>change last user ip</b><br>';
					$pagecontent .= ACPBox('userid', 'UserID', 'get user');
				}
			}elseif($_GET['acp_page'] == 'getuserid')
			{
				if(isset($_GET['username']))
				{
					$user = GetUserFromUsername($_GET['username']);
					$pagecontent .= Alert('userid: ' . $user['userid']);
				}else
				{
					$pagecontent .= '<b>get userid from username</b><br>';
					$pagecontent .= ACPBox('username', 'Username', 'get userid');
				}
			}elseif($_GET['acp_page'] == 'getuseride')
			{
				if(isset($_GET['email']))
				{
					$user = GetUserFromEmail($_GET['email']);
					$pagecontent .= Alert('userid: ' . $user['userid']);
				}else
				{
					$pagecontent .= '<b>get userid from email</b><br>';
					$pagecontent .= ACPBox('email', 'Email', 'get userid');
				}
			}elseif($_GET['acp_page'] == 'changespamtime')
			{
				if(isset($_GET['userid']))
				{
					if(isset($_POST['newvalue']))
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$query = "UPDATE users SET lastposted='" . PrepSQL($_POST['newvalue']) . "' WHERE userid='" . PrepSQL($_GET['userid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed lastposted from: ' . $user['username'] . ' to: ' . $_POST['newvalue']);
					}else
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$pagecontent .= Alert('username: ' . $user['username'] . ' currenttime: ' . time());
						$pagecontent .= ACPBoxUserID('newvalue', 'new lastposted time', 'change', $user['lastposted']);
					}				
				}else
				{
					$pagecontent .= '<b>change lastposted time</b><br>';
					$pagecontent .= ACPBox('userid', 'UserID', 'get user');
				}
			}elseif($_GET['acp_page'] == 'senduseremail')
			{
				if(isset($_GET['userid']))
				{
					if(isset($_POST['newvalue']))
					{
						$user = ACPUserIDDetails($_GET['userid']);
						SendEmail_Admin($user, $_POST['newvalue']);
						$pagecontent .= Alert('sent email to: ' . $user['useremail'] . ' with message: <br>' . $_POST['newvalue']);
					}else
					{
						$user = ACPUserIDDetails($_GET['userid']);
						$pagecontent .= Alert('username: ' . $user['username'] . ' email: ' . $user['useremail']);
						$pagecontent .= ACPBoxUserID('newvalue', 'email message', 'send', 'Helllo');
					}				
				}else
				{
					$pagecontent .= '<b>send email to user</b><br>';
					$pagecontent .= ACPBox('userid', 'UserID', 'get user');
				}
			//Comments
			}elseif($_GET['acp_page'] == 'changecomuser')
			{
				if(isset($_GET['postid']))
				{
					if(isset($_POST['newvalue']))
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$query = "UPDATE comments SET com_userid='" . PrepSQL($_POST['newvalue']) . "' WHERE com_id='" . PrepSQL($_GET['postid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed comment userid from: ' . $comment['com_userid'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
					}else
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
						$pagecontent .= ACPBoxPostID('newvalue', 'new comment userid', 'change', $comment['com_userid']);
					}				
				}else
				{
					$pagecontent .= '<b>change comment user</b><br>';
					$pagecontent .= ACPBox('postid', 'PostID', 'get comment');
				}
			}elseif($_GET['acp_page'] == 'changecomtime')
			{
				if(isset($_GET['postid']))
				{
					if(isset($_POST['newvalue']))
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$query = "UPDATE comments SET com_date='" . PrepSQL($_POST['newvalue']) . "' WHERE com_id='" . PrepSQL($_GET['postid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed comment date from: ' . $comment['com_date'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
					}else
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$pagecontent .= Alert('currenttime: ' . time());
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
						$pagecontent .= ACPBoxPostID('newvalue', 'new comment date', 'change', $comment['com_date']);
					}				
				}else
				{
					$pagecontent .= '<b>change comment date</b><br>';
					$pagecontent .= ACPBox('postid', 'PostID', 'get comment');
				}
			}elseif($_GET['acp_page'] == 'changecomcontent')
			{
				if(isset($_GET['postid']))
				{
					if(isset($_POST['newvalue']))
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$query = "UPDATE comments SET com_content='" . PrepSQL($_POST['newvalue']) . "' WHERE com_id='" . PrepSQL($_GET['postid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed comment content from: ' . $comment['com_content'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
					}else
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
						$pagecontent .= ACPBoxPostID('newvalue', 'new comment content', 'change', $comment['com_content']);
					}				
				}else
				{
					$pagecontent .= '<b>change comment content</b><br>';
					$pagecontent .= ACPBox('postid', 'PostID', 'get comment');
				}
			}elseif($_GET['acp_page'] == 'changecomreply')
			{
				if(isset($_GET['postid']))
				{
					if(isset($_POST['newvalue']))
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$query = "UPDATE comments SET com_replyid='" . PrepSQL($_POST['newvalue']) . "' WHERE com_id='" . PrepSQL($_GET['postid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed comment content from: ' . $comment['com_replyid'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
					}else
					{
						//Set null
						if(isset($_GET['setnull']) && $_GET['setnull'])
						{
							$comment = ACPComIDDetails($_GET['postid']);
							$query = "UPDATE comments SET com_replyid=NULL WHERE com_id='" . PrepSQL($_GET['postid']) . "' LIMIT 1";		
							$result = mysqli_query($GLOBALS['con'],$query);
							$pagecontent .= Alert('changed comment content from: ' . $comment['com_replyid'] . ' to: NULL');
							$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
						}else
						{
							$comment = ACPComIDDetails($_GET['postid']);
							$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
							$pagecontent .= ACPBoxPostID('newvalue', 'new replyid', 'change', $comment['com_replyid']);
							$pagecontent .= '<a href="./acp.php?acp_page=changecomreply&postid=' . $_GET['postid'] . '&setnull=1">set to null</a>';
						}
					}				
				}else
				{
					$pagecontent .= '<b>change comment replyid</b><br>';
					$pagecontent .= ACPBox('postid', 'PostID', 'get comment');
				}
			}elseif($_GET['acp_page'] == 'changecomratings')
			{
				if(isset($_GET['postid']))
				{
					if(isset($_POST['newvalue']))
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$query = "UPDATE comments SET com_rating='" . PrepSQL($_POST['newvalue']) . "' WHERE com_id='" . PrepSQL($_GET['postid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed comment rating from: ' . $comment['com_rating'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
					}else
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
						$pagecontent .= ACPBoxPostID('newvalue', 'new comment rating', 'change', $comment['com_rating']);
					}				
				}else
				{
					$pagecontent .= '<b>change comment rating</b><br>';
					$pagecontent .= ACPBox('postid', 'PostID', 'get comment');
				}
			}elseif($_GET['acp_page'] == 'changecompageid')
			{
				if(isset($_GET['postid']))
				{
					if(isset($_POST['newvalue']))
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$query = "UPDATE comments SET com_pageid='" . PrepSQL($_POST['newvalue']) . "',com_gameid=NULL,com_subid=NULL WHERE com_id='" . PrepSQL($_GET['postid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed pageid from: ' . $comment['com_pageid'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
					}else
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
						$pagecontent .= ACPBoxPostID('newvalue', 'new comment pageid', 'change', $comment['com_pageid']);
					}				
				}else
				{
					$pagecontent .= '<b>change comment pageid</b><br>';
					$pagecontent .= ACPBox('postid', 'PostID', 'get comment');
				}
			}elseif($_GET['acp_page'] == 'changecomgameid')
			{
				if(isset($_GET['postid']))
				{
					if(isset($_POST['newvalue']))
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$query = "UPDATE comments SET com_pageid=NULL,com_gameid='" . PrepSQL($_POST['newvalue']) . "',com_subid=NULL WHERE com_id='" . PrepSQL($_GET['postid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed gameid from: ' . $comment['com_gameid'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
					}else
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
						$pagecontent .= ACPBoxPostID('newvalue', 'new comment gameid', 'change', $comment['com_gameid']);
					}				
				}else
				{
					$pagecontent .= '<b>change comment gameid</b><br>';
					$pagecontent .= ACPBox('postid', 'PostID', 'get comment');
				}
			}elseif($_GET['acp_page'] == 'changecomsubid')
			{
				if(isset($_GET['postid']))
				{
					if(isset($_POST['newvalue']))
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$query = "UPDATE comments SET com_pageid=NULL,com_gameid=NULL,com_subid='" . PrepSQL($_POST['newvalue']) . "' WHERE com_id='" . PrepSQL($_GET['postid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed subid from: ' . $comment['com_subid'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
					}else
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
						$pagecontent .= ACPBoxPostID('newvalue', 'new comment subid', 'change', $comment['com_pageid']);
					}				
				}else
				{
					$pagecontent .= '<b>change comment subid</b><br>';
					$pagecontent .= ACPBox('postid', 'PostID', 'get comment');
				}
			}elseif($_GET['acp_page'] == 'changecomdeleted')
			{
				if(isset($_GET['postid']))
				{
					if(isset($_POST['newvalue']))
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$query = "UPDATE comments SET com_deleted='" . PrepSQL($_POST['newvalue']) . "' WHERE com_id='" . PrepSQL($_GET['postid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed comment deleted status from: ' . $comment['com_deleted'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
					}else
					{
						$comment = ACPComIDDetails($_GET['postid']);
						$pagecontent .= DrawCommentFromID($_GET['postid'], 'SLime_', false, false);
						$pagecontent .= ACPBoxPostID('newvalue', 'new deleted status', 'change', $comment['com_deleted']);
					}				
				}else
				{
					$pagecontent .= '<b>change comment deleted status</b><br>';
					$pagecontent .= ACPBox('postid', 'PostID', 'get comment');
				}
			//Submissions
			}elseif($_GET['acp_page'] == 'changesubuser')
			{
				if(isset($_GET['subid']))
				{
					if(isset($_POST['newvalue']))
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$query = "UPDATE submissions SET sub_userid='" . PrepSQL($_POST['newvalue']) . "' WHERE sub_id='" . PrepSQL($_GET['subid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed submission userid from: ' . $submission['sub_userid'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
					}else
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
						$pagecontent .= ACPBoxSubID('newvalue', 'new submission userid', 'change', $submission['sub_userid']);
					}				
				}else
				{
					$pagecontent .= '<b>change submission user</b><br>';
					$pagecontent .= ACPBox('subid', 'subid', 'get submission');
				}
			}elseif($_GET['acp_page'] == 'changesubtime')
			{
				if(isset($_GET['subid']))
				{
					if(isset($_POST['newvalue']))
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$query = "UPDATE submissions SET sub_date='" . PrepSQL($_POST['newvalue']) . "' WHERE sub_id='" . PrepSQL($_GET['subid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed submission date from: ' . $submission['sub_date'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
					}else
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$pagecontent .= Alert('currenttime: ' . time());
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
						$pagecontent .= ACPBoxSubID('newvalue', 'new submission date', 'change', $submission['sub_date']);
					}				
				}else
				{
					$pagecontent .= '<b>change submission date</b><br>';
					$pagecontent .= ACPBox('subid', 'subid', 'get submission');
				}
			}elseif($_GET['acp_page'] == 'changesubtitle')
			{
				if(isset($_GET['subid']))
				{
					if(isset($_POST['newvalue']))
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$query = "UPDATE submissions SET sub_title='" . PrepSQL($_POST['newvalue']) . "' WHERE sub_id='" . PrepSQL($_GET['subid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed submission content from: ' . $submission['sub_title'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
					}else
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
						$pagecontent .= ACPBoxSubID('newvalue', 'new submission title', 'change', $submission['sub_title']);
					}				
				}else
				{
					$pagecontent .= '<b>change submission title</b><br>';
					$pagecontent .= ACPBox('subid', 'subid', 'get submission');
				}
			}elseif($_GET['acp_page'] == 'changesubdesc')
			{
				if(isset($_GET['subid']))
				{
					if(isset($_POST['newvalue']))
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$query = "UPDATE submissions SET sub_content='" . PrepSQL($_POST['newvalue']) . "' WHERE sub_id='" . PrepSQL($_GET['subid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed submission content from: ' . $submission['sub_content'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
					}else
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
						$pagecontent .= ACPBoxSubID('newvalue', 'new submission description', 'change', $submission['sub_content']);
					}				
				}else
				{
					$pagecontent .= '<b>change submission description</b><br>';
					$pagecontent .= ACPBox('subid', 'subid', 'get submission');
				}
			}elseif($_GET['acp_page'] == 'changesubdesc')
			{
				if(isset($_GET['subid']))
				{
					if(isset($_POST['newvalue']))
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$query = "UPDATE submissions SET sub_content='" . PrepSQL($_POST['newvalue']) . "' WHERE sub_id='" . PrepSQL($_GET['subid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed submission content from: ' . $submission['sub_content'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
					}else
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
						$pagecontent .= ACPBoxSubID('newvalue', 'new submission description', 'change', $submission['sub_content']);
					}				
				}else
				{
					$pagecontent .= '<b>change submission description</b><br>';
					$pagecontent .= ACPBox('subid', 'subid', 'get submission');
				}
			}elseif($_GET['acp_page'] == 'changesubtype')
			{
				if(isset($_GET['subid']))
				{
					if(isset($_POST['newvalue']))
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$query = "UPDATE submissions SET sub_isipfs='" . PrepSQL($_POST['newvalue']) . "' WHERE sub_id='" . PrepSQL($_GET['subid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed submission type from: ' . $submission['sub_isipfs'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
					}else
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
						$pagecontent .= ACPBoxSubID('newvalue', 'new sub type (0 = Magnet, 1 = IPFS, 2 = Cherry)', 'change', $submission['sub_isipfs']);
					}				
				}else
				{
					$pagecontent .= '<b>change submission type</b><br>';
					$pagecontent .= ACPBox('subid', 'subid', 'get submission');
				}
			}elseif($_GET['acp_page'] == 'changebasesub')
			{
				if(isset($_GET['subid']))
				{
					if(isset($_POST['newvalue']))
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$query = "UPDATE submissions SET sub_basesub='" . PrepSQL($_POST['newvalue']) . "' WHERE sub_id='" . PrepSQL($_GET['subid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed submission type from: ' . $submission['sub_basesub'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
					}else
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
						$pagecontent .= ACPBoxSubID('newvalue', 'new base submission', 'change', $submission['sub_basesub']);
					}				
				}else
				{
					$pagecontent .= '<b>change cherry base submission</b><br>';
					$pagecontent .= ACPBox('subid', 'subid', 'get submission');
				}
			}elseif($_GET['acp_page'] == 'changesublink')
			{
				if(isset($_GET['subid']))
				{
					if(isset($_POST['newvalue']))
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$query = "UPDATE submissions SET sub_link='" . PrepSQL($_POST['newvalue']) . "' WHERE sub_id='" . PrepSQL($_GET['subid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed submission link/hash from: ' . $submission['sub_link'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
					}else
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
						$pagecontent .= ACPBoxSubID('newvalue', 'new link/hash', 'change', $submission['sub_link']);
					}				
				}else
				{
					$pagecontent .= '<b>change link/hash</b><br>';
					$pagecontent .= ACPBox('subid', 'subid', 'get submission');
				}
			}elseif($_GET['acp_page'] == 'changesubgameid')
			{
				if(isset($_GET['subid']))
				{
					if(isset($_POST['newvalue']))
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$query = "UPDATE submissions SET sub_gameid='" . PrepSQL($_POST['newvalue']) . "' WHERE sub_id='" . PrepSQL($_GET['subid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed gameid from: ' . $submission['sub_gameid'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
					}else
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
						$pagecontent .= ACPBoxSubID('newvalue', 'new submission gameid', 'change', $submission['sub_gameid']);
					}				
				}else
				{
					$pagecontent .= '<b>change submission gameid</b><br>';
					$pagecontent .= ACPBox('subid', 'subid', 'get submission');
				}
			}elseif($_GET['acp_page'] == 'changesubdeleted')
			{
				if(isset($_GET['subid']))
				{
					if(isset($_POST['newvalue']))
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$query = "UPDATE submissions SET sub_deleted='" . PrepSQL($_POST['newvalue']) . "' WHERE sub_id='" . PrepSQL($_GET['subid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('changed submission deleted status from: ' . $submission['sub_deleted'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
					}else
					{
						$submission = ACPSubIDDetails($_GET['subid']);
						$pagecontent .= DrawSubmissionFromID($_GET['subid'], false, false);
						$pagecontent .= ACPBoxSubID('newvalue', 'new deleted status', 'change', $submission['sub_deleted']);
					}				
				}else
				{
					$pagecontent .= '<b>change submission deleted status</b><br>';
					$pagecontent .= ACPBox('subid', 'subid', 'get submission');
				}
			//Games
			}elseif($_GET['acp_page'] == 'gamesuggest')
			{
				if(isset($_GET['addsuggest']) && is_numeric($_GET['addsuggest']))
				{
					if(isset($_POST['pageid']) && isset($_POST['letterindex']) && isset($_POST['gametitle']) && isset($_POST['gamedate']) && isset($_POST['content']) && isset($_POST['gamerating']))
					{
						if(isset($_POST['gamecover']) && $_POST['gamecover'] != null)
						{
							$gamecover = "'" . $_POST['gamecover'] . "'";
						}else
						{
							$gamecover = 'NULL';
						}
						$gameid = GameCount();
						$query = "INSERT INTO gamelist (pageid, gametitle, gamedate, gamedesc, gameindex, gameid, deleted, gamerating, gamecover) VALUES ('" . PrepSQL($_POST['pageid']) . "', '" . PrepSQL($_POST['gametitle']) . "', '" . PrepSQL($_POST['gamedate']) . "', '"  . PrepSQL($_POST['content']) . "', '" . PrepSQL(strtoupper($_POST['letterindex'])) . "', '" . PrepSQL($gameid) . "', '0', '" . PrepSQL($_POST['gamerating']) . "', " . PrepSQL($gamecover) . ")";
						$result = mysqli_query($GLOBALS['con'], $query);
						$query = "UPDATE suggestions SET deleted='1' WHERE suggestionid='" . PrepSQL($_GET['addsuggest']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Alert('SUCCESS!<br><a href="./acp.php?acp_page=gamesuggest">return</a>');
						//Be nice, send an email
						if(isset($_GET['userid']) && is_numeric($_GET['userid']))
						{
							SendEmail_GameApproval($_GET['userid'], $_POST['gametitle'], $gameid);
						}
					}else
					{
						$query = "SELECT * FROM suggestions WHERE suggestionid='" . $_GET['addsuggest'] . "'AND deleted='0' ORDER BY suggestionid LIMIT 1";
						$result = mysqli_query($GLOBALS['con'],$query);	
						$row = mysqli_fetch_assoc($result);
						//retain the userid so i can send a nice email :)
						if(isset($_GET['userid']) && is_numeric($_GET['userid']))
						{
							$pagecontent .= ACPSGameBox($row['pageid'], CreateLetterIndex($row['gametitle']), $row['gametitle'], $row['gamedate'], $row['gamedesc'], 2.5, null, '&userid=' . $_GET['userid']);
						}else
						{
							$pagecontent .= ACPSGameBox($row['pageid'], CreateLetterIndex($row['gametitle']), $row['gametitle'], $row['gamedate'], $row['gamedesc'], 2.5, null);
						}
					}
				}else
				{
					if(isset($_GET['delsuggest']) && is_numeric($_GET['delsuggest']))
					{
						$query = "UPDATE suggestions SET deleted='1' WHERE suggestionid='" . PrepSQL($_GET['delsuggest']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$pagecontent .= Error('Deleted suggestionid: ' . $_GET['delsuggest'], 'DISAPPROVED');
					}
					$pagecontent .= '<h2>Showing <40 suggestions out of: ' . CountSuggestions() . ' (' . CountSuggestionsDel() . ' are marked as deleted)</h2>';
					$query = "SELECT * FROM suggestions WHERE deleted='0' ORDER BY suggestionid LIMIT 40";
					$result = mysqli_query($GLOBALS['con'],$query);	
					while($row = mysqli_fetch_assoc($result))
					{
						$username = GetUserFromID($row['userid']);
						$pagecontent .= Alert('UserID: ' . $row['userid'] . ' (' . $username['username'] . ') suggests:' .
						'<br><img src="./cssimg/page_' . $row['pageid'] . '.gif">' .
						'<br>PageID:' . $row['pageid'] .
						'<br><b>Title: </b>' . $row['gametitle'] .
						'<br><b>Date: </b>' . $row['gamedate'] .
						'<br><b>Description: </b><br>' . $row['gamedesc'] .
						'<b><br><br><a href="./acp.php?acp_page=gamesuggest&addsuggest=' . $row['suggestionid'] . '&userid=' . $row['userid'] . '">Edit and approve</a>' .
						'<br><br><a href="./acp.php?acp_page=gamesuggest&delsuggest=' . $row['suggestionid'] . '">disapprove</a></b>');
					}
				}
			}elseif($_GET['acp_page'] == 'addgame')
			{
				if(isset($_POST['pageid']) && isset($_POST['letterindex']) && isset($_POST['gametitle']) && isset($_POST['gamedate']) && isset($_POST['content']) && isset($_POST['gamerating']))
				{
					if(ContainsText($_POST['pageid']) && ContainsText($_POST['letterindex']) && ContainsText($_POST['gametitle']) && ContainsText($_POST['gamedate']) && ContainsText($_POST['content']) && ContainsText($_POST['gamerating']))
					{
						if(isset($_POST['gamecover']) && $_POST['gamecover'] != null)
						{
							$gamecover = "'" . PrepSQL($_POST['gamecover']) . "'";
						}else
						{
							$gamecover = 'NULL';
						}
						$gameid = GameCount();
						$query = "INSERT INTO gamelist (pageid, gametitle, gamedate, gamedesc, gameindex, gameid, deleted, gamerating, gamecover) VALUES ('" . PrepSQL($_POST['pageid']) . "', '" . PrepSQL($_POST['gametitle']) . "', '" . PrepSQL(htmlspecialchars($_POST['gamedate'])) . "', '"  . PrepSQL($_POST['content']) . "', '" . PrepSQL(strtoupper($_POST['letterindex'])) . "', '" . PrepSQL($gameid) . "', '0', '" . PrepSQL($_POST['gamerating']) . "', " . $gamecover . ")";
						$result = mysqli_query($GLOBALS['con'], $query);
						$pagecontent .= Alert(htmlspecialchars($query));
					}else
					{
						$pagecontent .= Error('fill them all');
						$pagecontent .= ACPGameBox($_POST['pageid'], $_POST['letterindex'], $_POST['gametitle'], $_POST['gamedate'], $_POST['content'], $_POST['gamerating'], $_POST['gamecover']);
					}
				}else
				{
					$pagecontent .= ACPGameBox(null,"#",null,null,null,2.5,null);
				}
			/*
				function ACPRenderSubmissionBox($preid, $pretitle, $precontent, $prelink)
				{
					$pagecontent = '<form class="SSky_Form" action="./createsubmission.php?acp_page=' . $_GET['acp_page'] . '" method="post">';	
					$pagecontent .= '<b> GameID: </b><input type="text" name="gameid" value="' . $preid . '"><br>';
					$pagecontent .= '<b> Base Submission ID: </b><input type="text" name="basesubid" value="' . $preid . '"><br>';
					$pagecontent .= '<b> Submission Title (Max 64 chars): </b><input type="text" maxlength="64" name="subtitle" value="' . $pretitle . '"><br>';
					$pagecontent .= '<b> Submission Description (Max 5000 chars): </b><br><textarea rows="16" maxlength="5000" id="desc" name="content">' . $precontent . '</textarea><br>';
					$pagecontent .= '<b> URL: </b><input type="text" name="sublink" value="' . $prelink . '"><br>';
					$pagecontent .= '<input type="submit" value="Post Submission">';
					$pagecontent .= '</form>';
					return $pagecontent;
				}
			*/
			}elseif($_GET['acp_page'] == 'addshortcutsub')
			{
				
				if(isset($_POST['gameid']) && isset($_POST['subtitle']) && isset($_POST['content']) && isset($_POST['sublink']) && isset($_POST['basesubid']))
				{
					if(is_numeric($_POST['gameid']) && is_numeric($_POST['basesubid']) && ContainsText($_POST['subtitle']) && ContainsText($_POST['content']) && ContainsText($_POST['sublink']))
					{
						//jesus christ if this leaks, im seriously so fucked holy shit PLEASE don't leak
						$seedboxurl = CherryURL();
						
						$submissionid = SubmissionCount();
						$finallink = htmlspecialchars($_POST['sublink']);
						if(substr($_POST['sublink'], 0, strlen($seedboxurl)) == $seedboxurl)
						{
							$finallink = substr($_POST['sublink'], strlen($seedboxurl));							
						}
						$query = "INSERT INTO submissions (sub_id, sub_gameid, sub_rating, sub_content, sub_title, sub_link, sub_date, sub_userid, sub_deleted, sub_isipfs, sub_basesub) VALUES ('" . PrepSQL($submissionid) . "', '" . PrepSQL($_POST['gameid']) . "', '0', '" . PrepSQL(htmlspecialchars($_POST['content'])) . "', '" . PrepSQL(htmlspecialchars($_POST['subtitle'])) . "', '" . PrepSQL($finallink) . "', '" . time() . "', '0', '0', '2', '" . PrepSQL($_POST['basesubid']) . "')";
						$result = mysqli_query($GLOBALS['con'], $query);
						$pagecontent .= Alert($query);
					}else
					{
						$pagecontent .= Error('fill them all');
						$pagecontent .= ACPRenderSubmissionBox($_POST['gameid'], $_POST['subtitle'], $_POST['basesubid'], $_POST['subtitle'], $_POST['content'], $_POST['sublink']);
					}
				}else
				{
					if(isset($_GET['gameid']) && is_numeric($_GET['gameid']))
					{
						$game = GetGameFromID(floor($_GET['gameid']));
						$pagecontent .= Alert('Game: ' . $game['gametitle'] . ', ' . GenTitleForPageID($game['pageid']));
					}
					$pagecontent .= ACPRenderSubmissionBox($gameid, null, null, null, null);
					//function ACPRenderSubmissionBox($pretitle, $precontent, $prelink)
				}
			}elseif($_GET['acp_page'] == 'emptysuggest')
			{
				$pagecontent .= '<center><h1>Empty Suggestions</h1></center>';
				if(isset($_GET['confirm']) && isset($_GET['confirm']))
				{
					$result = mysqli_query($GLOBALS['con'],"TRUNCATE TABLE `suggestions`");	
					$pagecontent .= Alert('Done!');
				}else
				{
					$pagecontent .= '<center><a href="./acp.php?acp_page=emptysuggest&confirm=1">You sure? - Yes</a></center>';
				}
			}elseif($_GET['acp_page'] == 'changegametitle')
			{
				if(isset($_GET['gameid']))
				{
					if(isset($_POST['newvalue']))
					{
						$oldgame = ACPGameIDDetails($_GET['gameid']);
						$query = "UPDATE gamelist SET gametitle='" . PrepSQL($_POST['newvalue']) . "' WHERE gameid='" . PrepSQL($_GET['gameid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= Alert('changed game title from: ' . $oldgame['gametitle'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= ACPDrawGame($game);
					}else
					{
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= ACPDrawGame($game);
						$pagecontent .= ACPBoxGameID('newvalue', 'new game title', 'change', $game['gametitle']);
					}				
				}else
				{
					$pagecontent .= '<b>change game title</b><br>';
					$pagecontent .= ACPBox('gameid', 'gameid', 'get game');
				}
			}elseif($_GET['acp_page'] == 'changegamedate')
			{
				if(isset($_GET['gameid']))
				{
					if(isset($_POST['newvalue']))
					{
						$oldgame = ACPGameIDDetails($_GET['gameid']);
						$query = "UPDATE gamelist SET gamedate='" . PrepSQL($_POST['newvalue']) . "' WHERE gameid='" . PrepSQL($_GET['gameid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= Alert('changed game date from: ' . $oldgame['gamedate'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= ACPDrawGame($game);
					}else
					{
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= ACPDrawGame($game);
						$pagecontent .= 'e.g. 9th September, 1999';
						$pagecontent .= ACPBoxGameID('newvalue', 'new game date', 'change', $game['gamedate']);
					}				
				}else
				{
					$pagecontent .= '<b>change game date</b><br>';
					$pagecontent .= ACPBox('gameid', 'gameid', 'get game');
				}
			}elseif($_GET['acp_page'] == 'changegameindex')
			{
				if(isset($_GET['gameid']))
				{
					if(isset($_POST['newvalue']))
					{
						$oldgame = ACPGameIDDetails($_GET['gameid']);
						$query = "UPDATE gamelist SET gameindex='" . PrepSQL(strtoupper($_POST['newvalue'])) . "' WHERE gameid='" . PrepSQL($_GET['gameid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= Alert('changed game letter index from: ' . $oldgame['gameindex'] . ' to: ' . strtoupper($_POST['newvalue']));
						$pagecontent .= ACPDrawGame($game);
					}else
					{
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= ACPDrawGame($game);
						$pagecontent .= ACPBoxGameID('newvalue', 'new game letter index', 'change', $game['gameindex']);
					}				
				}else
				{
					$pagecontent .= '<b>change game letter index</b><br>';
					$pagecontent .= ACPBox('gameid', 'gameid', 'get game');
				}
			}elseif($_GET['acp_page'] == 'changegamedesc')
			{
				if(isset($_GET['gameid']))
				{
					if(isset($_POST['newvalue']))
					{
						$oldgame = ACPGameIDDetails($_GET['gameid']);
						$query = "UPDATE gamelist SET gamedesc='" . PrepSQL($_POST['newvalue']) . "' WHERE gameid='" . PrepSQL($_GET['gameid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= Alert('<b>changed game desc from: </b><br><pre>' . $oldgame['gamedesc'] . '</pre><br><br><b> to: </b><br><pre>' . $_POST['newvalue'] . '</pre>');
						$pagecontent .= ACPDrawGame($game);
					}else
					{
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= ACPDrawGame($game);
						$pagecontent .= ACPBoxGameID('newvalue', 'new game desc', 'change', $game['gamedesc']);
					}				
				}else
				{
					$pagecontent .= '<b>change game desc</b><br>';
					$pagecontent .= ACPBox('gameid', 'gameid', 'get game');
				}
			}elseif($_GET['acp_page'] == 'changegamepage')
			{
				if(isset($_GET['gameid']))
				{
					if(isset($_POST['newvalue']))
					{
						$oldgame = ACPGameIDDetails($_GET['gameid']);
						$query = "UPDATE gamelist SET pageid='" . PrepSQL($_POST['newvalue']) . "' WHERE gameid='" . PrepSQL($_GET['gameid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= Alert('changed game pageid from: ' . $oldgame['pageid'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= ACPDrawGame($game);
					}else
					{
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= ACPDrawGame($game);
						$pagecontent .= ACPBoxGameID('newvalue', 'new game pageid', 'change', $game['pageid']);
					}				
				}else
				{
					$pagecontent .= '<b>change game pageid</b><br>';
					$pagecontent .= ACPBox('gameid', 'gameid', 'get game');
				}
			}elseif($_GET['acp_page'] == 'deletegame')
			{
				if(isset($_GET['gameid']))
				{
					if(isset($_POST['newvalue']))
					{
						$oldgame = ACPGameIDDetails($_GET['gameid']);
						$query = "UPDATE gamelist SET deleted='" . PrepSQL($_POST['newvalue']) . "' WHERE gameid='" . PrepSQL($_GET['gameid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= Alert('changed deleted game status from: ' . $oldgame['deleted'] . ' to: ' . $_POST['newvalue']);
						$pagecontent .= ACPDrawGame($game);
					}else
					{
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= ACPDrawGame($game);
						$pagecontent .= ACPBoxGameID('newvalue', 'new game deleted status', 'change', $game['deleted']);
					}				
				}else
				{
					$pagecontent .= '<b>change game deleted status</b><br>';
					$pagecontent .= ACPBox('gameid', 'gameid', 'get game');
				}
			}elseif($_GET['acp_page'] == 'gamethumb')
			{
				if(isset($_GET['gameid']))
				{
					if(isset($_POST['newvalue']))
					{
						$oldgame = ACPGameIDDetails($_GET['gameid']);
						$query = "UPDATE gamelist SET gamecover='" . PrepSQL($_POST['newvalue']) . "' WHERE gameid='" . PrepSQL($_GET['gameid']) . "' LIMIT 1";		
						$result = mysqli_query($GLOBALS['con'],$query);
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= Alert('<b>changed game cover url from: </b><br><pre>' . $oldgame['gamecover'] . '</pre><br><br><b> to: </b><br><pre>' . $_POST['newvalue'] . '</pre>');
						$pagecontent .= ACPDrawGame($game);
					}else
					{
						$game = ACPGameIDDetails($_GET['gameid']);
						$pagecontent .= ACPDrawGame($game);
						$pagecontent .= ACPBoxGameID('newvalue', 'new game cover url', 'change', $game['gamecover']);
					}				
				}else
				{
					$pagecontent .= '<b>change game desc</b><br>';
					$pagecontent .= ACPBox('gameid', 'gameid', 'get game');
				}
			}elseif($_GET['acp_page'] == 'reports')
			{
				if(isset($_GET['delreport']) && is_numeric($_GET['delreport']))
				{
					$query = "UPDATE reports SET deleted='1' WHERE reportid='" . PrepSQL($_GET['delreport']) . "' LIMIT 1";		
					$result = mysqli_query($GLOBALS['con'],$query);
					$pagecontent .= Error('Deleted report: ' . $_GET['delreport'], 'DELETED');
				}
				$pagecontent .= '<h2>Showing <40 reports out of: ' . CountReports() . ' (' . CountReportsDel() . ' are marked as deleted)</h2>';
				$query = "SELECT * FROM reports WHERE deleted='0' ORDER BY reportid LIMIT 40";
				$result = mysqli_query($GLOBALS['con'],$query);	
				while($row = mysqli_fetch_assoc($result))
				{
					$username = GetUserFromID($row['userid']);
					$pagecontent .= Alert('UserID: ' . $row['userid'] . ' (' . $username['username'] . ') reported this for:' .
					'<br>' . $row['reason'] .
					'<br><br><a href="./acp.php?acp_page=reports&delreport=' . $row['reportid'] . '">delete report</a></b>');
					if($row['postid'] == NULL)
					{
						$pagecontent .= DrawSubmissionFromID($row['subid'], true, false);
					}else
					{
						$pagecontent .= DrawCommentFromID($row['postid'], 'SAqua_', true, false);
					}
					$pagecontent .= '<hr>';
				}
			}elseif($_GET['acp_page'] == 'emptyreports')
			{
				$pagecontent .= '<center><h1>Empty Reports</h1></center>';
				if(isset($_GET['confirm']) && isset($_GET['confirm']))
				{
					$result = mysqli_query($GLOBALS['con'],"TRUNCATE TABLE `reports`");	
					$pagecontent .= Alert('Done!');
				}else
				{
					$pagecontent .= '<center><a href="./acp.php?acp_page=emptyreports&confirm=1">You sure? - Yes</a></center>';
				}
			}else
			{
				$pagecontent .= Error("Menu doesn't exist");
			}
		}else
		{
			$pagecontent .= '<center><b><a href="./fileman.php" target="_blank">PHP file manager</a></b><br><br>';
			$pagecontent .= '<h2>Users:</h2>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=banuser">change ban status</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changeusername">change username</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changeemail">change email</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changepassword">change password</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changecreationdate">change date created</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changeactivation">change activated status</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changeadmin">change admin status</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changeip">change last user ip address</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=getuserid">get userid from username</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=getuseride">get userid from email</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changespamtime">change lastposted time</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=senduseremail">send email to user</a></b><br>';
			$pagecontent .= '<h2>Comments:</h2>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changecomuser">change user poster from postid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changecomtime">change time posted from postid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changecomcontent">change comment content from postid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changecomreply">change replyid from postid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changecomratings">change ratings from postid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changecompageid">change pageid from postid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changecomgameid">change gameid from postid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changecomsubid">change subid from postid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changecomdeleted">change deleted status from postid</a></b><br>';
			$pagecontent .= '<h2>Submissions:</h2>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changesubuser">change user poster from subid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changesubtime">change time posted from subid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changesubtitle">change submission title from subid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changesubdesc">change submission desc from subid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changesubtype">change submission type from subid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changebasesub">change base submission from subid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changesublink">change link/hash from subid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changesubratings">change ratings from subid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changesubgameid">change gameid from subid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changesubdeleted">change deleted status from subid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=addshortcutsub">create shortcut submission</a></b><br>';
			$pagecontent .= '<h2>Game Pages:</h2>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=gamesuggest">view game suggestions (' . CountSuggestions() . ')</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=emptysuggest">empty suggestions</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=addgame">add a game</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changegametitle">change game title</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changegamedate">change game date</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changegameindex">change game letter index</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changegamedesc">change game desc</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=changegamepage">change game pageid</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=deletegame">change game deleted status</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=gamethumb">change game thumbnail url</a></b><br>';
			$pagecontent .= '<h2>Reports:</h2>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=reports">view reports (' . CountReports() . ')</a></b><br>';
			$pagecontent .= '<b><a href="./acp.php?acp_page=emptyreports">empty reports</a></b><br>';
		}
	}else
	{
		$pagecontent .= Error("You need to be an Administrator to view the Admin Control Panel.");
		$pagecontent .= GenGoBack();
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