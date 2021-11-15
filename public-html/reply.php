<?php 
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	$DEF_Title = 'Reply';
	$DEF_Desc = 'Reply to a comment posted on MagnetGames!';
	//Main content is here on the page
	$pagecontent .= PageBegin();
	//Are you logged in?
	if(isset($_SESSION['session_userid']))
	{
		if(isset($_GET["postid"]) && is_numeric($_GET["postid"]))
		{
			$postid = floor($_GET["postid"]);
			if(CommentExists($postid))
			{
				$comment = GetCommentFromPostID($postid);
				$DEF_Desc = $comment['com_content'];
				$pagecontent .= '<center><h2> Replying to:</h2></center><hr>';
				//Draw comment from id is getting out of WHACK
				//Have to do it this way so the replies post in the correct section
				$query = "SELECT com_id,com_rating,com_content,com_date,com_userid,com_replyid,com_pageid,com_gameid,com_subid FROM comments WHERE com_deleted='0' AND com_id='" . PrepSQL($postid) . "'LIMIT 1";
				$result = mysqli_query($GLOBALS['con'],$query);
				$row = mysqli_fetch_assoc($result);
				if(isset($row))
				{
					$pagecontent .= RenderComment($row['com_userid'], $row['com_date'], $row['com_content'], $row['com_rating'], $row['com_id'], $row['com_replyid'], 'SAqua_', false, true);
				}else
				{
					$pagecontent .= Error("PHP: Comment doesn't exist");
				}
				
				$GLOBALS['pageid'] = $row['com_pageid'];
				$GLOBALS['gameid'] = $row['com_gameid'];
				$GLOBALS['subid'] = $row['com_subid'];
				$pagetype = GetPageType();
				$pagecontent .= RenderCommentBox(null, $pagetype, $postid);
				$pagecontent .= GenGoBack('#com_' . $postid);
			}else
			{
				$pagecontent .= Error("Comment doesn't exist!");
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