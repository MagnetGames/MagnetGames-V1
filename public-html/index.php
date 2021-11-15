<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	include './src/metadata.php'; //Contains the titles/descriptions of the all html based pages
	CheckPage();
	GenRedirect();
	//Main content is here on the page
	$pagecontent .= PageBegin();
	CheckGame();
	CheckSubmission();
	if(isset($GLOBALS['gameid']))
	{
		$tmp_game = GetGameFromID($GLOBALS['gameid']);
		$DEF_Title = $tmp_game['gametitle'];
		$DEF_Desc = $tmp_game['gamedesc'];
		//Cool preview thumbnails!
		if(file_exists('cssimg/games/game_' . $GLOBALS['gameid'] . '.gif'))
		{
			$pagecontent .= '<center><img src="./cssimg/games/game_' . $GLOBALS['gameid'] . '.gif" title="' . $tmp_game['gametitle'] . '" style="max-width: 320px;max-height: 320px"></img><br>';
		}elseif(file_exists('cssimg/games/game_' . $GLOBALS['gameid'] . '.jpg'))
		{
			$pagecontent .= '<center><img src="./cssimg/games/game_' . $GLOBALS['gameid'] . '.jpg" title="' . $tmp_game['gametitle'] . '" style="max-width: 320px;max-height: 320px"></img><br>';
		}elseif(isset($tmp_game['gamecover'])) //New external game covers!
		{
			$pagecontent .= '<center><img src="' . $tmp_game['gamecover'] . '" title="' . $tmp_game['gametitle'] . '" style="max-width: 320px;max-height: 320px"></img><br>';
		}else
		{
			$pagecontent .= '<center><img src="./cssimg/page_' . $tmp_game['pageid'] . '.gif"></img><br>';
		}
		$pagecontent .= '<h1>' . $tmp_game['gametitle'] . '</h1>';
		$pagecontent .= '<b>Initially released:</b> ' . $tmp_game['gamedate'].'<br>';
		if(isset($tmp_game['gamerating']))
		{
			$pagecontent .= '<b>Rating:</b> ' . $tmp_game['gamerating'] . '/5 stars<br>';
		}
		
		$pagecontent .= '</center><div style="margin: auto; text-align: center; padding: 8px;"><div class="SAqua_Description"><pre><b>Description:</b><br>' . $tmp_game['gamedesc'] . '</pre></div></div><hr>';
		$pagecontent .= DrawAdvertisements(null, '<br>'); //Add an ad
		//Are you logged in?
		if(isset($_SESSION['session_userid']))
		{
			$pagecontent .= '<center><a href="./createsubmission.php?gameid=' . $GLOBALS['gameid'] . '" class="Button">POST A SUBMISSION!</a></center>';
			if(IsUserAdmin($_SESSION['session_userid']))
			{
				$pagecontent .= '<center><a href="./acp.php?acp_page=addshortcutsub&gameid=' . $GLOBALS['gameid'] . '">ACP: Create Shortcut Submission</a></center>';
				$pagecontent .= '<center><a href="./acp.php?acp_page=changegametitle&gameid=' . $GLOBALS['gameid'] . '">ACP: Rename Game</a></center>';
			}
		}else
		{
			$pagecontent .= '<center><a href="./login.php?showdialog=1" class="Button">POST A SUBMISSION!</a></center>';
		}
		$pagecontent .= '<center><a href="./index.php?pageid=' . $tmp_game['pageid'] . '&pttl=' . GenTitle(GenTitleForPageID($tmp_game['pageid'])) . '#title_' . $GLOBALS['gameid'] . '" title="Go back to parent page!"><b>Go back to "' . GenTitleForPageID($tmp_game['pageid']) . '"</b></a><br></center>';
		$pagecontent .= SubmissionsBegin();
		$pagecontent .= DrawSubmissionsFromGame($tmp_game);
		$pagecontent .= SubmissionsEnd();
		$pagecontent .= CommentsBegin();
		$pagecontent .= RenderCommentBox(null, 'gameid', null);
		$pagecontent .= DrawCommentsFromPage($GLOBALS['gameid'], 'gameid');
		$pagecontent .= CommentsEnd();
	}else
	{
		$pagegamecount = PageGameCount($GLOBALS['pageid']);
		//Stats page
		if($GLOBALS['pageid'] == 606)
		{
			$pagecontent .= '<h1>Statistics:</h1><hr><div class="SAqua_AboutContent"><center>';
			$pagecontent .= '<img src="./cssimg/stats.gif">';
			$pagecontent .= '<br>Number of <b>Unique Site Visitors</b>: ' . GetStat('totaluniquevisits')['statcount'];
			$pagecontent .= '<br>Number of <b>Unique Site Visitors (Today)</b>: ' . GetStat('uniquevisitsday')['statcount'];
			$pagecontent .= '<br>Number of <b>Magnet-Link Submissions</b>: ' . GenericCount("submissions WHERE sub_isipfs='0' AND sub_deleted='0'");
			$pagecontent .= '<br>Number of <b>IPFS Submissions</b>: ' . GenericCount("submissions WHERE sub_isipfs='1' AND sub_deleted='0'");
			$pagecontent .= '<br>Number of <b>Cherry Submissions</b>: ' . GenericCount("submissions WHERE sub_isipfs='2' AND sub_deleted='0'");
			$pagecontent .= '<br>Number of <b>Total Comments</b>: ' . GenericCount("comments WHERE com_deleted='0'");
			$pagecontent .= '<br>Number of <b>Total Game/Software Titles</b>: ' . GenericCount("gamelist WHERE deleted='0'");
			$pagecontent .= '<br>Number of <b>Activated Users</b>: ' . GenericCount("users WHERE isactivated='1'");
			$pagecontent .= '<br>Number of <b>Unactivated Users</b>: ' . GenericCount("users WHERE isactivated='0'");
			$pagecontent .= '<br>Number of <b>Banned Users</b>: ' . GenericCount("users WHERE isbanned='1'");
			$pagecontent .= '</center></div>';
		}else
		{
			if($pagegamecount == 0)
			{
				$pagecontent .= DrawAdvertisements(null, '<br>'); //Add an ad
			}
			$pagecontent .= file_get_contents('./src/pages/page_' . $GLOBALS['pageid'] . '.html');
		}
		if($pagegamecount > 0)
		{
			$pagecontent .= DrawAdvertisements(); //Add an ad
			$pagecontent .= DrawGameHeader();
		}elseif($GLOBALS['pageid'] == 0)
		{
			//Music player!		
			srand(date("ymd")); //Daily seed
			//srand(time()); //Testing
			$songoftheday = rand(0, 6);
			$songname = null;
			$songfilename = null;
			
			switch ($songoftheday) 
			{				
				case 6:
					$songname = 'Legion Of The Lost - mxmaster';
					$songfilename = './cssimg/songs/Legion%20Of%20The%20Lost%20-%20mxmaster.mp3';
					break;
				case 5:
					$songname = 'Luna Waves';
					$songfilename = './cssimg/songs/luna.mp3';
					break;
				case 4:
					$songname = 'Goldrunner 2 - David Whittaker Remix';
					$songfilename = './cssimg/songs/bueno.mp3';
					break;
				case 3:
					$songname = 'You Could Do Better - T5P';
					$songfilename = './cssimg/songs/You%20Could%20Do%20Better%20-%20T5P.mp3';
					break;
				case 2:
					$songname = 'Her3 - estrayk ^ paradox';
					$songfilename = './cssimg/songs/her3.mp3';
					break;
				//The American
				case 1:
					$songname = 'The American - T5P';
					$songfilename = './cssimg/songs/The%20American%20-%20T5P.mp3';
					break;
				//The OG
				case 0:
				default: 
					$songname = 'Can\'t stop coming - ModArchive';
					$songfilename = './cssimg/songs/cant_stop_coming.mp3';
					break;
			}
			
			$pagecontent .= '<center><img src="cssimg\sotd.gif" title="Song of the Day!"><br>';
			$pagecontent .= '<audio controls loop preload="none"> <source src="' . $songfilename . '" type="audio/mpeg">Your browser does not support the audio element.</audio>';
			$pagecontent .= '<br>';
			$pagecontent .= '<i>' . $songname . '</i></center><br>';
		}
		
		//Comment Section
		$pagecontent .= CommentsBegin();
		$pagecontent .= RenderCommentBox(null, 'pageid', null);
		$pagecontent .= DrawCommentsFromPage($GLOBALS['pageid'], 'pageid');
		$pagecontent .= CommentsEnd();
		$DEF_Title = GenTitleForPageID($GLOBALS['pageid']);
		$DEF_Desc = GenDescForPageID($GLOBALS['pageid']);
	}
	//Add another ad
	$pagecontent .= DrawAdvertisements('<hr>');
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