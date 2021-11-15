<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/submissions.php';
	include './src/metadata.php';
	include './src/gamelist.php';	

	$pagecontent .= PageBegin();
	if(isset($_GET["postid"]) && is_numeric($_GET["postid"]))
	{
		if(CommentExists($_GET["postid"])) 
		{
			$comment = GetCommentFromPostID($_GET["postid"]);
			$DEF_Title = 'Viewing Comment ID: #' . $_GET["postid"];
			$DEF_Desc = $comment['com_content'];
			$pagecontent .=('<center><h2>Viewing Comment ID: ' . $_GET["postid"] . '</h2></center>');
			if(isset($comment['com_pageid']))
			{
				$pagecontent .=('<center><i><h3>Comment Source: <a href="./index.php?pageid=' . $comment['com_pageid'] . '&pttl=' . GenTitle(GenTitleForPageID($comment['com_pageid'])) . '#com_' . floor($_GET["postid"]) . '">' . GenTitleForPageID($comment['com_pageid']) . '</a></h3></i></center>');
			}elseif(isset($comment['com_subid']))
			{
				$sub = GetSubmissionFromPostID($comment['com_subid']);
				$pagecontent .=('<center><i><h3>Comment Source: <a href="./viewsubmission.php?postid=' . $comment['com_subid'] . '&pttl=' . GenTitle($sub['sub_title']) . '#com_' . floor($_GET["postid"]) . '">Submission #' . $comment['com_subid'] . ' (' . $sub['sub_title'] . ')</a></h3></i></center>');
			}elseif(isset($comment['com_gameid']))
			{
				$game = GetGameFromID($comment['com_gameid']);
				$pagecontent .=('<center><i><h3>Comment Source: <a href="./index.php?gameid=' . $comment['com_gameid'] . '&pttl=' . GenTitle($game['gametitle']) . '#com_' . floor($_GET["postid"]) . '">' . GenTitleForPageID($game['pageid']) . ' \\ ' . $game['gametitle'] . '</a></h3></i></center>');
			}
			$pagecontent .= DrawCommentFromID($_GET["postid"], 'SAqua_', true, true);
			$pagecontent .= GenGoBack('#com_' . $_GET["postid"]);
		}else
		{
			$DEF_Title = 'Comment ID: #' . $_GET["postid"] . " doesn't exist!";
			$DEF_Desc = 'View a MagnetGames Comment by PostID.';
			$pagecontent .= Error("This comment doesn't exist.");
			$pagecontent .= GenGoBack('#com_');
		}
	}else
	{
		$DEF_Title = 'Missing Page Arguments';
		$DEF_Desc = 'View a MagnetGames Comment by PostID.';
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