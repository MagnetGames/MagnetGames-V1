<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	include './src/metadata.php'; //Contains the titles/descriptions of the all html based pages
	//Main content is here on the page
	$pagecontent .= PageBegin();
	$DEF_Title = "Viewing Comments";
	//Loop through a page of comments
	if(isset($_GET["page"]) && is_numeric($_GET["page"]))
	{
		$page = $_GET["page"];
	}else
	{
		$page = 0;
	}
	//Maxpage count
	$maxpages = 20;
	GenRedirect();
	//Type of page
	$pagetype = GetPageType();
	$count = PageCommentCount($GLOBALS[$pagetype], $pagetype);
	//Is there even comments on the page?
	if($count > 0)
	{
		$pagecount = floor($count / $maxpages + 1);
		$pageurltitle = null;
		//Is the page count retarded?
		if($page > ($pagecount - 1) || $page < 0)
		{
			$page = 0;
		}
		if($pagetype == 'gameid') //Games
		{
			$game = GetGameFromID($GLOBALS['gameid']);
			$pageurltitle = GenTitle($game['gametitle']); //Neater URLS
			$DEF_Title = "Viewing Comments on Game '" . $game['gametitle'] . "'";
			$DEF_Desc = "Game '" . $game['gametitle'] . "' contains a total of " . $count . " comment(s).";
			$pagecontent .= '<center><h1><a href="./index.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&pttl=' . $pageurltitle . '">' . $game['gametitle'] . '</a> contains a total of <u>' . $count . '</u> comment(s).</h1></center>';
		}elseif($pagetype == 'subid') //Submissions
		{
			$submission = GetSubmissionFromPostID($GLOBALS['subid']);
			$pageurltitle = GenTitle($submission['sub_title']); //Neater URLS
			$DEF_Title = "Viewing Comments on Submission '" . $submission['sub_title'] . "'";
			$DEF_Desc = "Submission '" . $submission['sub_title'] . "' contains a total of " . $count . " comment(s).";
			$pagecontent .= '<center><h1><a href="./viewsubmission.php?postid=' . $GLOBALS[$pagetype] . '&pttl=' . $pageurltitle . '">Submission</a> contains a total of <u>' . $count . '</u> comment(s).</h1></center>';
			$pagecontent .= DrawSubmissionFromID($GLOBALS['subid'], false, true);
		}else //Pages
		{
			$pageurltitle = GenTitle(GenTitleForPageID($GLOBALS['pageid'])); //Neater URLS
			$DEF_Title = "Viewing Comments on Page '" . GenTitleForPageID($GLOBALS['pageid']) . "'";
			$DEF_Desc = "Page '" . GenTitleForPageID($GLOBALS['pageid']) . "' contains a total of " . $count . " comment(s).";
			$pagecontent .= '<center><h1><a href="./index.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&pttl=' . $pageurltitle . '#comments">' . GenTitleForPageID($GLOBALS['pageid']) . '</a> contains a total of <u>' . $count . '</u> comment(s).</h1></center>';
		}
			
		$pagecontent .= '<center><h2>Page <u>' . ($page + 1) . '</u> of <u>' . $pagecount . '</u>.</h2>';

		//Page numbers
		$pagecontent .= 'View page: ';
		for ($tmp_pagenum = 0; $tmp_pagenum < $pagecount; $tmp_pagenum++) 
		{
			//Add bold tag if it's the current page
			if($page == $tmp_pagenum)
			{
				$pagecontent .= '<b>';
			}
			if(isset($_GET["sortby"]) && (strtolower($_GET["sortby"]) == 'newest' || strtolower($_GET["sortby"]) == 'oldest' || strtolower($_GET["sortby"]) == 'upvotes' || strtolower($_GET["sortby"]) == 'downvotes'))
			{
				$pagecontent .= '<a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] .'&page=' . $tmp_pagenum . '&sortby=' . $_GET["sortby"] . '&pttl=' . $pageurltitle . '">Page ' . ($tmp_pagenum + 1) . '</a>';
			}else
			{
				$pagecontent .= '<a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] .'&page=' . $tmp_pagenum . '&pttl=' . $pageurltitle . '">Page ' . ($tmp_pagenum + 1) . '</a>';
			}
			//End bold tag (if it's the current page)
			if($page == $tmp_pagenum)
			{
				$pagecontent .= '</b>';
			}
			//Seperate them witth some commas
			if($tmp_pagenum < ($pagecount - 1))
			{
				$pagecontent .= ', ';
			}
		} 
		
		//Order by argument
		if(isset($_GET["sortby"]))
		{
			if(strtolower($_GET["sortby"]) == 'oldest')
			{
				$commentcolour = 'SAqua_';
				$sortby = 'ORDER BY com_id';
				$pagecontent .= '<br>Sort by: <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=newest' . '&pttl=' . $pageurltitle . '">Newest</a>, <b>Oldest</b>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=upvotes' . '&pttl=' . $pageurltitle . '"><a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=upvotes' . '&pttl=' . $pageurltitle . '">Upvotes</a></a>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=downvotes' . '&pttl=' . $pageurltitle . '">Downvotes</a></center><br>';
			}elseif(strtolower($_GET["sortby"]) == 'upvotes')
			{
				$commentcolour = 'SLime_';
				$sortby = 'ORDER BY com_rating DESC';
				$pagecontent .= '<br>Sort by: <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=newest' . '&pttl=' . $pageurltitle . '">Newest</a>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=oldest' . '&pttl=' . $pageurltitle . '">Oldest</a>, <b>Upvotes</b>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=downvotes' . '&pttl=' . $pageurltitle . '">Downvotes</a></center><br>';
			}elseif(strtolower($_GET["sortby"]) == 'downvotes')
			{
				$commentcolour = 'SRed_';
				$sortby = 'ORDER BY com_rating';
				$pagecontent .= '<br>Sort by: <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=newest' . '&pttl=' . $pageurltitle . '">Newest</a>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=oldest' . '&pttl=' . $pageurltitle . '">Oldest</a>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=upvotes' . '&pttl=' . $pageurltitle . '">Upvotes</a>, <b>Downvotes</b></center><br>';
			}else //newest
			{
				$commentcolour = 'SAqua_';
				$sortby = 'ORDER BY com_id DESC';
				$pagecontent .= '<br>Sort by: <b>Newest</b>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=oldest' . '&pttl=' . $pageurltitle . '">Oldest</a>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=upvotes' . '&pttl=' . $pageurltitle . '">Upvotes</a>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=downvotes' . '&pttl=' . $pageurltitle . '">Downvotes</a></center><br>';
			}
		}else
		{
			$commentcolour = 'SAqua_';
			$sortby = 'ORDER BY com_id DESC';
			$pagecontent .= '<br>Sort by: <b>Newest</b>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=oldest' . '&pttl=' . $pageurltitle . '">Oldest</a>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=upvotes' . '&pttl=' . $pageurltitle . '">Upvotes</a>, <a href="./viewcomments.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&page=' . $page . '&sortby=downvotes' . '&pttl=' . $pageurltitle . '">Downvotes</a></center><br>';
		}
		$pagecontent .= RenderCommentBox(null, $pagetype, null);
		$pagecontent .= '<br>';
		//Draw the comments 
		$query = "SELECT com_id,com_rating,com_content,com_date,com_userid,com_replyid FROM comments WHERE com_deleted='0' AND com_" . $pagetype . "='" . PrepSQL($GLOBALS[$pagetype]) . "' " . $sortby . " LIMIT " . PrepSQL($maxpages) . " OFFSET " . PrepSQL($maxpages * $page);
		$result = mysqli_query($GLOBALS['con'],$query);
		while($row = mysqli_fetch_assoc($result))
		{
			$pagecontent .= RenderComment($row['com_userid'], $row['com_date'], $row['com_content'], $row['com_rating'], $row['com_id'], $row['com_replyid'], $commentcolour, true, true);
		}
	}else
	{
		if($pagetype == 'gameid')
		{
			$pagecontent .= '<center>There are currently no comments on this game.<br><b>Why not be the first?</b></center>';
		}elseif($pagetype == 'subid')
		{
			$pagecontent .= '<center>There are currently no comments on this submission.<br><b>Why not be the first?</b></center>';
			$pagecontent .= DrawSubmissionFromID($GLOBALS['subid'], false, true);
		}else
		{
			$pagecontent .= '<center>There are currently no comments on this page.<br><b>Why not be the first?</b></center>';
		}
		
		
		$pagecontent .= RenderCommentBox(null, $pagetype, null);
		$pagecontent .= GenGoBack();
	}
	$pagecontent .= PageEnd();
	//Add the header content to the beginning of the page.
	$headercontent = GenMeta($DEF_Title, $DEF_Desc);
	$headercontent .= file_get_contents($DEF_Header);
	$headercontent .= file_get_contents($DEF_Navbar);
	$headercontent .= DrawUserBar();
	//Finalise page
	echo $headercontent . $pagecontent;
?>