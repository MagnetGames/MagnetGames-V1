<?php 
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	$DEF_Title = 'Report Post';
	$DEF_Desc = 'Report a Post that goes against the MagnetGames Community Guidelines.';
	//Main content is here on the page
	$pagecontent .= PageBegin();
	
	//Renders the box the lets you post a submission
	function RenderReportBox($precontent)
	{
		$pagecontent = '<form class="SRed_Form" action="./report.php?postid=' . $_GET["postid"] . '" method="post">';	
		$pagecontent .= '<b> Report reason (Max 5000 chars): </b><br><textarea rows="16" maxlength="5000" id="desc" name="content">' . $precontent . '</textarea><br>';
		$pagecontent .= '<input type="submit" value="Report Comment">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function RenderSubReportBox($precontent)
	{
		$pagecontent = '<form class="SRed_Form" action="./report.php?subid=' . $_GET["subid"] . '" method="post">';	
		$pagecontent .= '<b> Report reason (Max 5000 chars): </b><br><textarea rows="16" maxlength="5000" id="desc" name="content">' . $precontent . '</textarea><br>';
		$pagecontent .= '<input type="submit" value="Report Submission">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function GetReport($reportid)
	{
		$query = "SELECT * FROM reports WHERE reportid='" . PrepSQL($reportid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	function ReportCount()
	{
		$query = "SELECT COUNT(*) FROM reports LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function HasReportedPost($tmp_postid)
	{
		$query = "SELECT COUNT(*) FROM reports WHERE userid='" . PrepSQL($_SESSION['session_userid']) . "' AND postid='" . PrepSQL($tmp_postid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function HasReportedSub($tmp_postid)
	{
		$query = "SELECT COUNT(*) FROM reports WHERE userid='" . PrepSQL($_SESSION['session_userid']) . "' AND subid='" . PrepSQL($tmp_postid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	//Are you logged in?
	if(isset($_SESSION['session_userid']))
	{
		if(isset($_GET["postid"]) && is_numeric($_GET["postid"]))
		{
			$postid = floor($_GET["postid"]);
			if(CommentExists($postid))
			{
				//Have you reported it already?
				if(HasReportedPost($postid))
				{
					$pagecontent .= Error("You can't report the same content twice!");
					$pagecontent .= GenGoBack();
				}else
				{
					//Has posted something?
					if(isset($_POST["content"]))
					{
						$content = htmlspecialchars(substr($_POST["content"], 0, 5000)); 
						if(GetLastPostTime($_SESSION['session_userid']) >= (time() - 30))
						{
							$pagecontent .= Error("Please wait 30 seconds before posting again.");
							$pagecontent .= '<center><h2> Reporting comment:</h2></center><hr>';
							$pagecontent .= DrawCommentFromID($postid, 'SAqua_', true, false);
							$pagecontent .= RenderReportBox($content);
							$pagecontent .= GenGoBack();					
						}elseif(ContainsText($content))
						{
							$pagecontent .= Alert('<b>Successfully reported comment!<br>Reason:</b><br>' . $content);
							$pagecontent .= DrawCommentFromID($postid, 'SRed_', true, false);
							//Redirect
							$pagecontent .= '<center><b>Redirecting...</b></center><br>';
							$pagecontent .= '<meta http-equiv="Refresh" content="4; url=./' . $GLOBALS['lastpage'] . '#com_' . $postid . '">';
							
							$reportid = ReportCount();
							$query = "INSERT INTO reports (reportid, userid, deleted, postid, subid, reason) VALUES ('" . PrepSQL($reportid) . "', '" . PrepSQL($_SESSION['session_userid']) . "', '0', '" . PrepSQL($postid) . "', NULL, '" . PrepSQL($_POST["content"]) .  "')";					
							$result = mysqli_query($GLOBALS['con'], $query);
							//Anti-SPam
							SetLastPostTime($_SESSION['session_userid']);
						}else
						{
							$pagecontent .= Error('Please add a valid reason!');
							$pagecontent .= '<center><h2> Reporting comment:</h2></center><hr>';
							$pagecontent .= DrawCommentFromID($postid, 'SAqua_', true, false);
							$pagecontent .= RenderReportBox($content);
							$pagecontent .= GenGoBack('#com_' . $postid);
						}
					}else
					{
						$pagecontent .= '<center><h2> Reporting comment:</h2></center><hr>';
						$pagecontent .= Error("We support free speech and you can be as toxic as you want, but personal attacks such as <b></u>doxing</u></b> will absolutely not be tolerated!<br>
						We can't police everything, so please refrain reporting if a user is just being a simple bully or troll so MagnetGames can focus on more serious comments such as personal attacks.<br>
						Use the downvote button instead if you don't agree with someone.<br><br>
						If someone is spamming, directly posting copyrighted links, or linking malicious websites then feel free to report it.<br>
						You can read more about it on our " . '<a href="./index.php?pageid=601"><b>community guidelines page.</b></a>', 'Remember!');
						$pagecontent .= DrawCommentFromID($postid, 'SAqua_', true, false);
						$pagecontent .= RenderReportBox(null);
						$pagecontent .= GenGoBack('#com_' . $postid);
					}
				}
			}else
			{
				$pagecontent .= Error("Comment doesn't exist!");
				$pagecontent .= GenGoBack();
			}
		}elseif(isset($_GET["subid"]) && is_numeric($_GET["subid"]))
		{
			$subid = floor($_GET["subid"]);
			if(SubmissionExists($subid))
			{
				//Have you reported it already?
				if(HasReportedSub($subid))
				{
					$pagecontent .= Error("You can't report the same content twice!");
					$pagecontent .= GenGoBack();
				}else
				{
					//Has posted something?
					if(isset($_POST["content"]))
					{
						$content = htmlspecialchars(substr($_POST["content"], 0, 5000)); 
						if(GetLastPostTime($_SESSION['session_userid']) >= (time() - 30))
						{
							$pagecontent .= Error("Please wait 30 seconds before posting again.");
							$pagecontent .= RenderSubReportBox($content);
							$pagecontent .= GenGoBack();					
						}elseif(ContainsText($content))
						{
							$pagecontent .= Alert('<b>Successfully reported submission!<br>Reason:</b><br>' . $content);
							$pagecontent .= DrawSubmissionFromID($subid, 'SRed_', true, false);
							//Redirect
							$pagecontent .= '<center><b>Redirecting...</b></center><br>';
							$pagecontent .= '<meta http-equiv="Refresh" content="4; url=./' . $GLOBALS['lastpage'] . '#sub_' . $subid . '">';
							
							$reportid = ReportCount();
							$query = "INSERT INTO reports (reportid, userid, deleted, subid, postid, reason) VALUES ('" . PrepSQL($reportid) . "', '" . PrepSQL($_SESSION['session_userid']) . "', '0', '" . PrepSQL($subid) . "', NULL, '" . PrepSQL($_POST["content"]) .  "')";					
							$result = mysqli_query($GLOBALS['con'], $query);
							//Anti-SPam
							SetLastPostTime($_SESSION['session_userid']);
						}else
						{
							$pagecontent .= Error('Please add a valid reason!');
							$pagecontent .= '<center><h2> Reporting submission:</h2></center><hr>';
							$pagecontent .= DrawSubmissionFromID($subid, true, false);
							$pagecontent .= RenderSubReportBox($content);
							$pagecontent .= GenGoBack('#com_' . $subid);
						}
					}else
					{
						$pagecontent .= '<center><h2> Reporting submission:</h2></center><hr>';
						$pagecontent .= Error("Submissions can be anything as long as it's related to the game.<br>If a submission contains something completely unrelated, or contains harmful content feel free to report it.<br>
						You can read more about it on our " . '<a href="./index?pageid=601"><b>community guidelines page.</b></a>', 'Remember!');
						$pagecontent .= DrawSubmissionFromID($subid, true, false);
						$pagecontent .= RenderSubReportBox(null);
						$pagecontent .= GenGoBack('#com_' . $subid);
					}
				}
			}else
			{
				$pagecontent .= Error("Submission doesn't exist!");
				$pagecontent .= GenGoBack();
			}
		}else
		{
			$pagecontent .= Error("Missing or invalid argument.");
			$pagecontent .= GenGoBack();
		}
	}else
	{
		$pagecontent .= Error("You're not logged in.... Wait how did you get here?");
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