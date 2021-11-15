<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	$DEF_Title = 'Viewing Submissions';
	$pagecontent .= PageBegin();
	//Does the game exist?
	if(isset($GLOBALS['gameid']))
	{
		GenRedirect();
		//Loop through a bunch of submissions
		if(isset($_GET["page"]))
		{
			$page = $_GET["page"];
		}else
		{
			$page = 0;
		}
		//Maxpage count
		$maxpages = 20;
		$count = GameSubmissionCount($GLOBALS['gameid']);
		//Is there even submissions on the page?
		if($count > 0)
		{
			$pagecount = floor($count / $maxpages + 1);
			//Is the page count retarded?
			if($page > ($pagecount - 1) || $page < 0 || !is_numeric($page))
			{
				$page = 0;
			}
			$game = GetGameFromID($GLOBALS['gameid']);
			$DEF_Title = "Viewing Submissions on Game '" . $game['gametitle'] . "'";
			$DEF_Desc = "Game '" . $game['gametitle'] . "' contains a total of " . $count . " submissions(s).";
			$pagecontent .= '<center><h1><a href="./index.php?gameid=' . $GLOBALS['gameid'] . '&pttl=' . $game['gametitle'] . '">' . $game['gametitle'] . '</a> contains a total of <u>' . $count . '</u> submissions.</h1>';
			$pagecontent .= '<h2>Page <u>' . ($page + 1) . '</u> of <u>' . $pagecount . '</u>.</h2>';

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
					$pagecontent .= '<a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] .'&page=' . $tmp_pagenum . '&sortby=' . $_GET["sortby"] . '&pttl=' . $game['gametitle'] . '">Page ' . ($tmp_pagenum + 1) . '</a>';
				}else
				{
					$pagecontent .= '<a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] .'&page=' . $tmp_pagenum . '&pttl=' . $game['gametitle'] . '">Page ' . ($tmp_pagenum + 1) . '</a>';
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
					$sortby = 'ORDER BY sub_id';
					$pagecontent .= '<br>Sort by: <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=newest' . '&pttl=' . $game['gametitle'] . '">Newest</a>, <b>Oldest</b>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=upvotes' . '&pttl=' . $game['gametitle'] . '"><a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=upvotes' . '&pttl=' . $game['gametitle'] . '">Upvotes</a></a>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=downvotes' . '&pttl=' . $game['gametitle'] . '">Downvotes</a></center><br>';
				}elseif(strtolower($_GET["sortby"]) == 'upvotes')
				{
					$sortby = 'ORDER BY sub_rating DESC';
					$pagecontent .= '<br>Sort by: <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=newest' . '&pttl=' . $game['gametitle'] . '">Newest</a>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=oldest' . '&pttl=' . $game['gametitle'] . '">Oldest</a>, <b>Upvotes</b>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=downvotes' . '&pttl=' . $game['gametitle'] . '">Downvotes</a></center><br>';
				}elseif(strtolower($_GET["sortby"]) == 'downvotes')
				{
					$sortby = 'ORDER BY sub_rating';
					$pagecontent .= '<br>Sort by: <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=newest' . '&pttl=' . $game['gametitle'] . '">Newest</a>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=oldest' . '&pttl=' . $game['gametitle'] . '">Oldest</a>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=upvotes' . '&pttl=' . $game['gametitle'] . '">Upvotes</a>, <b>Downvotes</b></center><br>';
				}else //Newest
				{
					$sortby = 'ORDER BY sub_id DESC';
					$pagecontent .= '<br>Sort by: <b>Newest</b>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=oldest' . '&pttl=' . $game['gametitle'] . '">Oldest</a>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=upvotes' . '&pttl=' . $game['gametitle'] . '">Upvotes</a>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=downvotes' . '&pttl=' . $game['gametitle'] . '">Downvotes</a></center><br>';
				}
			}else
			{
				$sortby = 'ORDER BY sub_id DESC';
				$pagecontent .= '<br>Sort by: <b>Newest</b>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=oldest' . '&pttl=' . $game['gametitle'] . '">Oldest</a>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=upvotes' . '&pttl=' . $game['gametitle'] . '">Upvotes</a>, <a href="./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&page=' . $page . '&sortby=downvotes' . '&pttl=' . $game['gametitle'] . '">Downvotes</a></center><br>';
			}
			$pagecontent .= '<br>';
			//Draw the submissions
			
			$query = "SELECT * FROM submissions WHERE sub_deleted='0' AND sub_gameid='" . PrepSQL($GLOBALS['gameid']) . "' " . $sortby . " LIMIT " . PrepSQL($maxpages) . " OFFSET " . PrepSQL($maxpages * $page);
			$result = mysqli_query($GLOBALS['con'],$query);
			while($row = mysqli_fetch_assoc($result))
			{
				switch($row['sub_isipfs'])
				{					
					case 0: //Magnet Link
						$pagecontent .= RenderSubMagnet($row['sub_userid'], $row['sub_date'], $row['sub_content'], $row['sub_rating'], $row['sub_id'], $row['sub_title'], $row['sub_link'], 'SGold_', true, true);
						break;
					case 1: //IPFS
						$pagecontent .= RenderSubIPFS($row['sub_userid'], $row['sub_date'], $row['sub_content'], $row['sub_rating'], $row['sub_id'], $row['sub_title'], $row['sub_link'], 'SPurple_', true, true);
						break;
					case 2: //Seedbox
						$pagecontent .= RenderSubSeedbox($row['sub_date'], $row['sub_content'], $row['sub_rating'], $row['sub_id'], $row['sub_title'], $row['sub_basesub'], 'SSky_', true, true);
						break;
				}
			}	
		}else
		{
			$pagecontent .= '<center>There are currently no submissions for this game.<br><b>Why not be the first?</b></center>';
		}
	}else
	{
		$pagecontent .= Error("Missing arguments or game doesn't exist.");
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