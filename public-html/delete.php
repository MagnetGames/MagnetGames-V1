<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/submissions.php';
	$DEF_Title = 'Delete a Post';
	$pagecontent .= PageBegin();
	//Are you logged in?
	if(isset($_SESSION['session_userid']))
	{
		if(isset($_GET["postid"]) && is_numeric($_GET["postid"]))
		{
			if(GetUserIDFromPostID($_GET["postid"]) == $_SESSION['session_userid']) 
			{
				if(isset($_GET["confirm"]) && $_GET["confirm"] == 1)
				{
					$pagecontent .= '<center><h2>Comment Deleted!</h2><b>Redirecting...</b></center><br>';
					$pagecontent .= '<meta http-equiv="Refresh" content="2; url=./' . $GLOBALS['lastpage'] . '">';
					DeleteCommentFromID($_GET["postid"]);
				}else
				{
					$pagecontent .= '<center>Are you sure you want to delete this comment?<br><b>This cannot be undone!</b></center><br>';
					$pagecontent .= DrawCommentFromID($_GET["postid"], 'SRed_', true, false);
					$pagecontent .= '<center><h2><a href="' . htmlspecialchars(basename($_SERVER['REQUEST_URI'])) . '&confirm=1">Confirm</a></h2></center>';
					$pagecontent .= GenGoBack('#com_' . $_GET["postid"]);
				}
			}else
			{
				$pagecontent .= Error("This comment doesn't belong to you or it doesn't exist.");
				$pagecontent .= GenGoBack('#com_' . $_GET["postid"]);
			}
		}elseif(isset($_GET["subid"]) && is_numeric($_GET["subid"]))
		{
			if(GetUserIDFromSubID($_GET["subid"]) == $_SESSION['session_userid']) 
			{
				if(isset($_GET["confirm"]) && $_GET["confirm"] == 1)
				{
					$pagecontent .= '<center><h2>Submission Deleted!</h2><b>Redirecting...</b></center><br>';
					$pagecontent .= '<meta http-equiv="Refresh" content="2; url=./' . $GLOBALS['lastpage'] . '">';
					DeleteSubmissionFromID($_GET["subid"]);
				}else
				{
					$pagecontent .= '<center>Are you sure you want to delete this submission?<br><b>This cannot be undone!</b></center><br>';
					$pagecontent .= DrawSubmissionFromID($_GET["subid"], true, false);
					$pagecontent .= '<center><h2><a href="' . htmlspecialchars(basename($_SERVER['REQUEST_URI'])) . '&confirm=1">Confirm</a></h2></center>';
					$pagecontent .= GenGoBack('#sub_' . $_GET["subid"]);
				}
			}else
			{
				$pagecontent .= Error("This submission doesn't belong to you or it doesn't exist.");
				$pagecontent .= GenGoBack('#com_' . $_GET["subid"]);
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