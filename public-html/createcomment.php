<?php 
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	include './src/email.php';
	CheckPage();
	$DEF_Title = 'Post a comment';
	$DEF_Desc = 'Create a comment on a page or post.';
	//Main content is here on the page
	$pagecontent .= PageBegin();
	//TOOODOOO
	//ADD LOGIN CHECK 
	//OTHERWISE SOMEONE CAN INPERSONATE USER 0 === BAD
	//Type of page
	$pagetype = GetPageType();
	if(isset($_POST["content"]) && isset($_SESSION['session_userid']))
	{
		$com_content = htmlspecialchars(substr($_POST["content"], 0, 2000)); 
		if(isset($_GET["replyid"]))
		{
			$replyid = $_GET["replyid"];
		}else
		{
			$replyid = null;
		}
		if(!ContainsText($com_content)) //Checks if the only thing posted is spaces
		{
			$pagecontent .= Error("No text was detected in post.");
			$pagecontent .= RenderCommentBox($com_content, GetPageType(), $replyid);
			$pagecontent .= GenGoBack();
		}elseif(GetLastPostTime($_SESSION['session_userid']) >= (time() - 30))
		{
			$pagecontent .= Error("Please wait 30 seconds before posting again.");
			$pagecontent .= RenderCommentBox($com_content, GetPageType(), $replyid);
			$pagecontent .= GenGoBack();
		}else
		{
			//Get the comment ID
			$commentid = CommentCount();
			//Is there a reply arg?
			if(isset($_GET["replyid"]) && is_numeric($_GET["replyid"]) && CommentExists($_GET["replyid"]))
			{
				$query = "INSERT INTO comments (com_id, com_" . $pagetype . ", com_rating, com_content, com_date, com_userid, com_replyid) VALUES ('" . PrepSQL($commentid) . "', '" . PrepSQL($GLOBALS[$pagetype]) . "', '0', '" . PrepSQL($com_content) . "', '" . time() .  "', '" . PrepSQL($_SESSION['session_userid']) .  "', '" . PrepSQL($_GET["replyid"]) . "')";	
				$result = mysqli_query($GLOBALS['con'], $query);
				//Let them know they replied
				SendEmail_Reply($commentid);
			}else
			{
				$query = "INSERT INTO comments (com_id, com_" . $pagetype . ", com_rating, com_content, com_date, com_userid) VALUES ('" . PrepSQL($commentid) . "', '" . PrepSQL($GLOBALS[$pagetype]) . "', '0', '" . PrepSQL($com_content) . "', '" . time() .  "', '" . PrepSQL($_SESSION['session_userid']) .  "')";					
				$result = mysqli_query($GLOBALS['con'], $query);
				//Send the submission guy an email since this fella commented on it
				if($pagetype == 'subid')
				{
					SendEmail_Sub($commentid);
				}
			}
			
			$pagecontent .= '<center><h3>Comment #' . $commentid . ' posted successfully!</h3></center>';
			$pagecontent .= DrawCommentFromID($commentid, 'SAqua_', true, true);
			//Redirect
			$pagecontent .= '<center><b>Redirecting...</b></center>';
			$pagecontent .= '<meta http-equiv="Refresh" content="2; url=./' . $GLOBALS['lastpage'] . '#com_' . $commentid . '">';
			//Anti-SPam
			SetLastPostTime($_SESSION['session_userid']);
		}
	}else
	{
		$pagecontent .= Error("Missing POST request or not logged in!");
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