<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	include './src/metadata.php'; //Contains the titles/descriptions of the all html based pages
	$pagecontent .= PageBegin();

	if(isset($_GET["postid"]) && is_numeric($_GET["postid"]))
	{
		if(SubmissionExists($_GET["postid"])) 
		{
			GenRedirect();
			$gameid = GetGameIDFromSubID($_GET["postid"]);
			$gamename = GetGameNameFromID($gameid);
			$GLOBALS['subid'] = $_GET["postid"]; //Fixes the comment box
			//More details about this submission ;)
			$submission = GetSubmissionFromPostID($_GET["postid"]);
			$DEF_Title = "Viewing Submission '" . $submission['sub_title'] . "'";
			$DEF_Desc = $submission['sub_content'];
			$pagecontent .= '<center><h2>Viewing submission ID: ' . $_GET["postid"] . '</h2><b><a href="./index.php?gameid=' . $gameid . '&pttl=' . GenTitle($gamename) . '#sub_' . $_GET["postid"] . '">Return to &quot' . $gamename . '&quot Page</a><br>';
			$pagecontent .= '<a href="./viewsubmissions.php?gameid=' . $gameid . '&pttl=' . GenTitle($gamename) . '#sub_' . $_GET["postid"] . '">Return to &quot' . $gamename . '&quot Submissions</a></b></center><br>';
			$pagecontent .= DrawSubmissionFromID($_GET["postid"], false, true);
			$pagecontent .= CommentsBegin();
			$pagecontent .= RenderCommentBox(null, 'subid', null);
			$pagecontent .= DrawCommentsFromPage($_GET["postid"], 'subid');
			$pagecontent .= CommentsEnd();
		}else
		{
			$DEF_Title = 'Submission ID: #' . $_GET["postid"] . " doesn't exist!";
			$DEF_Desc = 'View a MagnetGames Submission by PostID.';
			$pagecontent .= Error("This submission doesn't exist.");
			$pagecontent .= GenGoBack();
		}
	}else
	{
		$DEF_Title = 'Missing Page Arguments';
		$DEF_Desc = 'View a MagnetGames Submission by PostID.';
		$pagecontent .= Error("Missing or invalid argument.");
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