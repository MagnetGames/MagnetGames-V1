<?php
	function SubmissionCount()
	{
		$query = "SELECT COUNT(*) FROM submissions LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function GameSubmissionCount($gameid)
	{
		$query = "SELECT COUNT(*) FROM submissions WHERE sub_deleted='0' AND sub_gameid='" . PrepSQL($gameid) . "'";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	//Just basic on the fly submission headers etc
	function SubmissionsBegin()
	{
		$pagecontent = '<div id="submissions">';
			$pagecontent .= '<div>';
				$pagecontent .= '<h2>Top Submissions:</h2><hr>';
				//$pagecontent .= Error("MagnetGames is not responsible how the contents in Magnet Links/IPFS Hashes are used in any way!<br>If your computer is damaged by misuse of Magnet Links/IPFS Hashes submitted by our users...<br><b><i>That's entirely YOUR FAULT FOR NOT RESEARCHING!</i></b>", 'DISCLAIMER');
				//$pagecontent .= '<hr>';
			$pagecontent .= '</div>';		
		return $pagecontent;
	}
	function SubmissionsEnd()
	{
		return '</div>';
	}
	//Renders the box the lets you post a submission
	function RenderSubmissionBox($pretitle, $precontent, $prelink)
	{
		$pagecontent = '<form class="SSky_Form" action="./createsubmission.php?gameid=' . $GLOBALS['gameid'] . '&submit=1" method="post">';	
		$pagecontent .= '<b> Submission Title (Max 64 chars): </b><input type="text" maxlength="64" name="subtitle" value="' . $pretitle . '"><br>';
		$pagecontent .= '<b> Submission Description (Max 5000 chars): </b><br><textarea rows="16" maxlength="5000" id="desc" name="content">' . $precontent . '</textarea><br>';
		$pagecontent .= '<b> Magnet Link/IPFS Hash (Max 5000/46 chars): </b><input type="text" maxlength="5000" name="sublink" value="' . $prelink . '"><br>';
		$pagecontent .= '<span title="Warning: You won&rsquo;t be able to edit/remove your submission under this setting!"><input type="checkbox" name="subanon"><label for="subanon"><b>Post Anonymously?</b></label></span>';
		$pagecontent .= '<input type="submit" value="Post Submission">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function CheckSubmission()
	{
		if(isset($_GET["subid"]) && is_numeric($_GET["subid"]))
		{	
			$GLOBALS['subid'] = floor($_GET["subid"]);
			if(!SubmissionExists($GLOBALS['subid']))
			{
				$GLOBALS['subid'] = null;
				$GLOBALS['pageid'] = 404;
			}
		}else
		{
			$GLOBALS['subid'] = null;
		}
	}
	function GetGameIDFromSubID($tmp_postid)
	{
		$query = "SELECT sub_gameid FROM submissions WHERE sub_deleted='0' AND sub_id='" . PrepSQL($tmp_postid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['sub_gameid'];
	}
	function GetUserIDFromSubID($tmp_postid)
	{
		$query = "SELECT sub_userid FROM submissions WHERE sub_deleted='0' AND sub_id='" . PrepSQL($tmp_postid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['sub_userid'];
	}
	//Marks a submission as deleted
	function DeleteSubmissionFromID($sub_id)
	{
		$query = "UPDATE submissions SET sub_deleted='1', sub_content='Deleted', sub_userid='0' WHERE sub_id='" . PrepSQL($sub_id) . "' LIMIT 1";		
		$result = mysqli_query($GLOBALS['con'],$query);
	}
	function SubmissionExists($sub_id)
	{
		$query = "SELECT COUNT(*) FROM submissions WHERE sub_deleted='0' AND sub_id='" . PrepSQL($sub_id) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	//Gets the submission from id
	function GetSubmissionFromPostID($tmp_postid)
	{
		$query = "SELECT * FROM submissions WHERE sub_deleted='0' AND sub_id='" . PrepSQL($tmp_postid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	//Renders a cool submission epic aweesome style
	function RenderSubMagnet($sub_userid, $sub_date, $sub_content, $sub_votes, $sub_id, $sub_title, $sub_link, $arg_style, $arg_drawvcom, $arg_drawreport) 
	{
		$sub_user = GetUserFromID($sub_userid);
		$sub_username = $sub_user['username'];
		$sub_badges = RenderBadges($sub_user);
		$date = getdate($sub_date);
		
		$pagecontent = '<div class="' . $arg_style . 'Submission" id="sub_' . $sub_id . '">';
			$pagecontent .= '<div>';
				$pagecontent .= '<div class="' . $arg_style . 'SubmissionTitle">';
					$pagecontent .= '<center>';
						$pagecontent .= '<a href="./index.php?pageid=604&pttl=using-magnet-links" target="_blank"><img src="./cssimg/magnet.png" title="Magnet Link"></a> ';
						$pagecontent .= '<a href="' . $sub_link . '">' . $sub_title . '</a>';
					$pagecontent .= '</center>';
				$pagecontent .= '</div>';
				$pagecontent .= '<center>Link above does nothing? <i><b><a href="./index.php?pageid=604&pttl=using-magnet-links#clients" target="_blank" title="Guide on Magnet Links!">You need to download a torrent client!</a></b></i></center>';
				$pagecontent .= '<div class="' . $arg_style . 'SubmissionHeader">';
					$pagecontent .= '<b><center>';
						//Avatar
						$pagecontent .= '<a href="./profile.php?username=' . $sub_username . '" title="View Profile!">' . get_gravatar($sub_user['useremail'], 48, $arg_style . 'CAvatar') . '</a><br>';
						$pagecontent .= $sub_badges . '<a href="./profile.php?username=' . $sub_username . '" title="View Profile!">' . $sub_username . '</a> </b> <a href="./viewsubmission.php?postid=' . $sub_id . '&pttl=' . GenTitle($sub_title) . '" target="_blank" title="View Magnet Link Submission from Post ID">#' . $sub_id . '</a> <b> - <span title="UTC - DD/MM/YYYY">(' . TimeToString($date['hours'], $date['minutes']) . ' - ' . $date['mday']. '/' . $date['mon']. '/' . $date['year'] . ')</span>';
					$pagecontent .= '</center></b>';
				$pagecontent .= '</div>';
				$pagecontent .= '<div class="SubmissionContent"><pre>';
					$pagecontent .= $sub_content;
				$pagecontent .= '</pre></div>';
			$pagecontent .= '</div>';
			$pagecontent .= '<div class="' . $arg_style . 'SubmissionRatings">';
				$pagecontent .= '<b>';
					//Show view comments?
					if($arg_drawvcom)
					{
						$pagecontent .= '<a href="./viewsubmission.php?postid=' . $sub_id . '&pttl=' . GenTitle($sub_title) . '">View Comments (' . SubCommentCount($sub_id) . ')</a>';
					}
					//Is logged in?
					if(isset($_SESSION['session_userid']))
					{
						if($arg_drawvcom && $arg_drawreport)
						{
							$pagecontent .= ' - ';
						}
						if($arg_drawreport)
						{
							if($sub_userid == $_SESSION['session_userid'])
							{
								$pagecontent .= '<a href="./delete.php?subid=' . $sub_id . '">Delete Submission</a>';
							}else
							{
								$pagecontent .= '<a href="./report.php?subid=' . $sub_id . '">Report This Submission</a>';
							}
						}
					}else
					{
						if($arg_drawvcom && $arg_drawreport)
						{
							$pagecontent .= ' - ';
						}
						if($arg_drawreport)
						{
							$pagecontent .= '<a href="./login.php?showdialog=1">Report</a>';				
						}
					}					
					// Upvote/Downvote colours
					if($sub_votes > 0)
					{
						$pagecontent .= ' - <span class="CommentUV" id="svotes_' . $sub_id . '">+' . $sub_votes . ' </span>';
					}
					elseif($sub_votes < 0)
					{
						$pagecontent .= ' - <span class="CommentDV" id="svotes_' . $sub_id . '">' . $sub_votes . ' </span>';
					}
					else
					{
						$pagecontent .= ' - <span id="svotes_' . $sub_id . '">0 </span>';
					}
					
					//Enlarge the vote buttons for mobile
					if(IsMobile())
					{
						$MobileZoomTags = 'style="zoom: 2;"';
					}else
					{
						$MobileZoomTags = null;
					}
					//Is logged in
					if(isset($_SESSION['session_userid']))
					{
						$HasUserVoted = SHasUserVoted($sub_id, $_SESSION['session_userid']);
						//Never voted
						if($HasUserVoted == null || $HasUserVoted == 'del')
						{
							$pagecontent .= '<img src="./cssimg/thumb_gup.png" title="Upvote" id="suv_' . $sub_id . '" onclick="SUpvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_gdown.png" title="Downvote" id="sdv_' . $sub_id . '" onclick="SDownvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}elseif($HasUserVoted) //Has Upvoted
						{
							$pagecontent .= '<img src="./cssimg/thumb_up.png" title="Upvote" id="suv_' . $sub_id . '" onclick="SUpvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_gdown.png" title="Downvote" id="sdv_' . $sub_id . '" onclick="SDownvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}else //Has Downvoted
						{
							$pagecontent .= '<img src="./cssimg/thumb_gup.png" title="Upvote" id="suv_' . $sub_id . '" onclick="SUpvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_down.png" title="Downvote" id="sdv_' . $sub_id . '" onclick="SDownvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}
					}else
					{
						$pagecontent .= '<a href="./login.php?showdialog=1"><img src="./cssimg/thumb_gup.png" title="Upvote" ' . $MobileZoomTags . '></a> ';
						$pagecontent .= '<a href="./login.php?showdialog=1"><img src="./cssimg/thumb_gdown.png" title="Downvote" ' . $MobileZoomTags . '></a> ';
					}
				$pagecontent .= '</b>';
			$pagecontent .= '</div>';
		$pagecontent .= '</div>';
		return $pagecontent;
	}
	function RenderSubIPFS($sub_userid, $sub_date, $sub_content, $sub_votes, $sub_id, $sub_title, $sub_link, $arg_style, $arg_drawvcom, $arg_drawreport) 
	{
		$sub_user = GetUserFromID($sub_userid);
		$sub_username = $sub_user['username'];
		$sub_badges = RenderBadges($sub_user);
		$date = getdate($sub_date);
		
		$pagecontent = '<div class="' . $arg_style . 'Submission" id="sub_' . $sub_id . '">';
			$pagecontent .= '<div>';
				$pagecontent .= '<div class="' . $arg_style . 'SubmissionTitle">';
					$pagecontent .= '<center>';
						$pagecontent .= '<a href="./index.php?pageid=605&pttl=using-ipfs" target="_blank"><img src="./cssimg/ipfs.png" title="IPFS Hash"></a> ';
						$pagecontent .= '<a href="https://ipfs.io/ipfs/' . $sub_link . '" target="_blank">' . $sub_title . '</a>';
					$pagecontent .= '</center>';
				$pagecontent .= '</div>';
				$pagecontent .= '<div id="subhash_' . $sub_id . '" class="' . $arg_style . 'SubmissionHash" onClick="SelectText(' . "'subhash_" . $sub_id . "'" . ');"><center><b>' . $sub_link . '</center></b></div>';
				$pagecontent .= '<div class="' . $arg_style . 'SubmissionHeader">';
					$pagecontent .= '<b><center>';
						//Avatar
						$pagecontent .= '<a href="./profile.php?username=' . $sub_username . '" title="View Profile!">' . get_gravatar($sub_user['useremail'], 48, $arg_style . 'CAvatar') . '</a><br>';
						$pagecontent .= $sub_badges . '<a href="./profile.php?username=' . $sub_username . '" title="View Profile!">' . $sub_username . '</a> </b> <a href="./viewsubmission.php?postid=' . $sub_id . '&pttl=' . GenTitle($sub_title) . '" target="_blank" title="View IPFS Submission from Post ID">#' . $sub_id . '</a> <b> - <span title="UTC - DD/MM/YYYY">(' . TimeToString($date['hours'], $date['minutes']) . ' - ' . $date['mday']. '/' . $date['mon']. '/' . $date['year'] . ')</span>';
					$pagecontent .= '</center></b>';
				$pagecontent .= '</div>';
				$pagecontent .= '<div class="SubmissionContent"><pre>';
					$pagecontent .= $sub_content;
				$pagecontent .= '</pre></div>';
			$pagecontent .= '</div>';
			$pagecontent .= '<div class="' . $arg_style . 'SubmissionRatings">';
				$pagecontent .= '<b>';
					//Show view comments?
					if($arg_drawvcom)
					{
						$pagecontent .= '<a href="./viewsubmission.php?postid=' . $sub_id . '&pttl=' . GenTitle($sub_title) . '">View Comments (' . SubCommentCount($sub_id) . ')</a>';
					}
					//Is logged in?
					if(isset($_SESSION['session_userid']))
					{
						if($arg_drawvcom && $arg_drawreport)
						{
							$pagecontent .= ' - ';
						}
						if($arg_drawreport)
						{
							if($sub_userid == $_SESSION['session_userid'])
							{
								$pagecontent .= '<a href="./delete.php?subid=' . $sub_id . '">Delete Submission</a>';
							}else
							{
								$pagecontent .= '<a href="./report.php?subid=' . $sub_id . '">Report This Submission</a>';
							}
						}
					}else
					{
						if($arg_drawvcom && $arg_drawreport)
						{
							$pagecontent .= ' - ';
						}
						if($arg_drawreport)
						{
							$pagecontent .= '<a href="./login.php?showdialog=1">Report</a>';				
						}
					}					
					// Upvote/Downvote colours
					if($sub_votes > 0)
					{
						$pagecontent .= ' - <span class="CommentUV" id="svotes_' . $sub_id . '">+' . $sub_votes . ' </span>';
					}
					elseif($sub_votes < 0)
					{
						$pagecontent .= ' - <span class="CommentDV" id="svotes_' . $sub_id . '">' . $sub_votes . ' </span>';
					}
					else
					{
						$pagecontent .= ' - <span id="svotes_' . $sub_id . '">0 </span>';
					}
					
					//Enlarge the vote buttons for mobile
					if(IsMobile())
					{
						$MobileZoomTags = 'style="zoom: 2;"';
					}else
					{
						$MobileZoomTags = null;
					}
					//Is logged in
					if(isset($_SESSION['session_userid']))
					{
						$HasUserVoted = SHasUserVoted($sub_id, $_SESSION['session_userid']);
						//Never voted
						if($HasUserVoted == null || $HasUserVoted == 'del')
						{
							$pagecontent .= '<img src="./cssimg/thumb_gup.png" title="Upvote" id="suv_' . $sub_id . '" onclick="SUpvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_gdown.png" title="Downvote" id="sdv_' . $sub_id . '" onclick="SDownvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}elseif($HasUserVoted) //Has Upvoted
						{
							$pagecontent .= '<img src="./cssimg/thumb_up.png" title="Upvote" id="suv_' . $sub_id . '" onclick="SUpvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_gdown.png" title="Downvote" id="sdv_' . $sub_id . '" onclick="SDownvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}else //Has Downvoted
						{
							$pagecontent .= '<img src="./cssimg/thumb_gup.png" title="Upvote" id="suv_' . $sub_id . '" onclick="SUpvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_down.png" title="Downvote" id="sdv_' . $sub_id . '" onclick="SDownvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}
					}else
					{
						$pagecontent .= '<a href="./login.php?showdialog=1"><img src="./cssimg/thumb_gup.png" title="Upvote" ' . $MobileZoomTags . '></a> ';
						$pagecontent .= '<a href="./login.php?showdialog=1"><img src="./cssimg/thumb_gdown.png" title="Downvote" ' . $MobileZoomTags . '></a> ';
					}
				$pagecontent .= '</b>';
			$pagecontent .= '</div>';
		$pagecontent .= '</div>';
		return $pagecontent;
	}
	//Individual Files from torrents
	function RenderSubSeedbox($sub_date, $sub_content, $sub_votes, $sub_id, $sub_title, $sub_base, $arg_style, $arg_drawvcom, $arg_drawreport) 
	{
		$sub_basedon = GetSubmissionFromPostID($sub_base);
		$sub_basedonuser = GetUserFromID($sub_basedon['sub_userid']);
		$date = getdate($sub_date);
		
		$pagecontent = '<div class="' . $arg_style . 'Submission" id="sub_' . $sub_id . '">';
			$pagecontent .= '<div>';
				$pagecontent .= '<div class="' . $arg_style . 'SubmissionTitle">';
					$pagecontent .= '<center>';
						$pagecontent .= '<img src="./cssimg/cherrypick.gif" title="Cherry-picked Magnet Submission"> ';
						$pagecontent .= '<a href="./cherrypicker.php?subid=' . $sub_id . '" target="_blank" title="Download ROM" id="Download ROM">' . $sub_title . '</a>';
					$pagecontent .= '</center>';
				$pagecontent .= '</div>';
				$pagecontent .= '<div class="' . $arg_style . 'SubmissionHash"><center><img src="./cssimg/shortcut.gif" title="Submission Shortcut"> Imported from <b><a href="./profile.php?username=' . $sub_basedonuser['username']  . '" title="View Profile!">' . $sub_basedonuser['username']  . '\'s</a></b> submission: <b>' . '<a href="./viewsubmission.php?postid=' . $sub_basedon['sub_id'] . '&pttl=' . GenTitle($sub_basedon['sub_title']) . '" target="_blank">' . $sub_basedon['sub_title'] . '</a>' . '</b></center></div>';
				$pagecontent .= '<div class="' . $arg_style . 'SubmissionHeader">';
					$pagecontent .= '<center><b>SeedBox</b> ';
						$pagecontent .= '<a href="./viewsubmission.php?postid=' . $sub_id . '&pttl=' . GenTitle($sub_title) . '" target="_blank" title="View Cherry Submission from Post ID">#' . $sub_id . '</a> <b> - <span title="UTC - DD/MM/YYYY">(' . TimeToString($date['hours'], $date['minutes']) . ' - ' . $date['mday']. '/' . $date['mon']. '/' . $date['year'] . ')</span></b>';
					$pagecontent .= '</center>';
				$pagecontent .= '</div>';
				$pagecontent .= '<div class="SubmissionContent"><pre>';
					$pagecontent .= $sub_content;
				$pagecontent .= '</pre></div>';
			$pagecontent .= '</div>';
			$pagecontent .= '<div class="' . $arg_style . 'SubmissionRatings">';
				$pagecontent .= '<b>';
					//Show view comments?
					if($arg_drawvcom)
					{
						$pagecontent .= '<a href="./viewsubmission.php?postid=' . $sub_id . '&pttl=' . GenTitle($sub_title) . '">View Comments (' . SubCommentCount($sub_id) . ')</a>';
					}				
					// Upvote/Downvote colours
					if($sub_votes > 0)
					{
						$pagecontent .= ' - <span class="CommentUV" id="svotes_' . $sub_id . '">+' . $sub_votes . ' </span>';
					}
					elseif($sub_votes < 0)
					{
						$pagecontent .= ' - <span class="CommentDV" id="svotes_' . $sub_id . '">' . $sub_votes . ' </span>';
					}
					else
					{
						$pagecontent .= ' - <span id="svotes_' . $sub_id . '">0 </span>';
					}
					
					//Enlarge the vote buttons for mobile
					if(IsMobile())
					{
						$MobileZoomTags = 'style="zoom: 2;"';
					}else
					{
						$MobileZoomTags = null;
					}
					//Is logged in
					if(isset($_SESSION['session_userid']))
					{
						$HasUserVoted = SHasUserVoted($sub_id, $_SESSION['session_userid']);
						//Never voted
						if($HasUserVoted == null || $HasUserVoted == 'del')
						{
							$pagecontent .= '<img src="./cssimg/thumb_gup.png" title="Upvote" id="suv_' . $sub_id . '" onclick="SUpvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_gdown.png" title="Downvote" id="sdv_' . $sub_id . '" onclick="SDownvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}elseif($HasUserVoted) //Has Upvoted
						{
							$pagecontent .= '<img src="./cssimg/thumb_up.png" title="Upvote" id="suv_' . $sub_id . '" onclick="SUpvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_gdown.png" title="Downvote" id="sdv_' . $sub_id . '" onclick="SDownvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}else //Has Downvoted
						{
							$pagecontent .= '<img src="./cssimg/thumb_gup.png" title="Upvote" id="suv_' . $sub_id . '" onclick="SUpvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_down.png" title="Downvote" id="sdv_' . $sub_id . '" onclick="SDownvote(' . $sub_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}
					}else
					{
						$pagecontent .= '<a href="./login.php?showdialog=1"><img src="./cssimg/thumb_gup.png" title="Upvote" ' . $MobileZoomTags . '></a> ';
						$pagecontent .= '<a href="./login.php?showdialog=1"><img src="./cssimg/thumb_gdown.png" title="Downvote" ' . $MobileZoomTags . '></a> ';
					}
				$pagecontent .= '</b>';
			$pagecontent .= '</div>';
		$pagecontent .= '</div>';
		return $pagecontent;
	}
	function DrawSubmissionFromID($sub_id, $arg_drawvcom, $arg_drawreport)
	{
		$query = "SELECT * FROM submissions WHERE sub_deleted='0' AND sub_id='" . PrepSQL($sub_id) . "'LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		$pagecontent = null;
		if(isset($row))
		{
			//Submission Types!
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
		}else
		{
			$pagecontent .= Error("PHP: Submission doesn't exist");
		}
		return $pagecontent;
	}
	//From full page
	Function DrawSubmissionsFromGame($game)
	{
		$count = GameSubmissionCount($game['gameid']);
		
		$pagecontent = null;
		//Wait, there's no submissions at all?
		if($count == 0)
		{
			$pagecontent .= '<center>There are currently no submissions for this game.<br><b>Why not be the first?</b></center>';
		}else
		{
			$query = "SELECT * FROM submissions WHERE sub_deleted='0' AND sub_gameid='" . PrepSQL($game['gameid']) . "' ORDER BY sub_rating DESC LIMIT 5";
			$result = mysqli_query($GLOBALS['con'],$query);
			while($row = mysqli_fetch_assoc($result))
			{
				//Submission Types!
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
			//Add view all submissions link
			if($count >= 5)
			{
				$pagecontent .= '<center>Showing <u>5</u> submissions out of <u>' . $count . '</u>.';
			}else
			{
				$pagecontent .= '<center>Showing <u>' . $count . '</u> submissions out of <u>' . $count . '</u>.';
			}
			$pagecontent .= '<br><b><a href="./viewsubmissions.php?gameid=' . $game['gameid'] . '&pttl=' . GenTitle($game['gametitle']) . '">View all submissions</a></b></center>';
		}
		return $pagecontent;
	}
	//Votes
	function SHasUserVoted($subid, $srcuserid)
	{
		$query = "SELECT thumbedup,deleted FROM votes WHERE sub_id='" . PrepSQL($subid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		if($row['deleted'])
		{
			return 'del';
		}else
		{
			return $row['thumbedup'];
		}
	}
	//Upvote
	function SUpvote($subid, $srcuserid)
	{
		$hasVoted = SHasUserVoted($subid, $srcuserid);
		if($hasVoted == null) //Never voted this post EVER
		{
			//Create a row about upvoting
			$query = "INSERT INTO votes (userid, sub_id, thumbedup) VALUES ('" . PrepSQL($srcuserid) . "', '" . PrepSQL($subid) . "', True)";					
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE submissions SET sub_rating = sub_rating + 1 WHERE sub_id='" . PrepSQL($subid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif($hasVoted == 'del') //Wants to reupvote
		{
			//Mark the vote as upvoted + uNdeleted
			$query = "UPDATE votes SET deleted = 0, thumbedup = True WHERE sub_id='" . PrepSQL($subid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE submissions SET sub_rating = sub_rating + 1 WHERE sub_id='" . PrepSQL($subid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif($hasVoted) //Is currently upvoted already
		{
			//Mark the vote as deleted and take 1 vote
			$query = "UPDATE votes SET deleted = 1, thumbedup = True WHERE sub_id='" . PrepSQL($subid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE submissions SET sub_rating = sub_rating - 1 WHERE sub_id='" . PrepSQL($subid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif(!$hasVoted) //Is currently downvoted already
		{
			//Upvote twice to undo the downvote
			$query = "UPDATE votes SET deleted = 0, thumbedup = True WHERE sub_id='" . PrepSQL($subid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE submissions SET sub_rating = sub_rating + 2 WHERE sub_id='" . PrepSQL($subid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}else
		{
			$pagecontent .= Error('Big fuckup! AAAAAAAAA');
		}
	}
	//Downvotes
	function SDownvote($subid, $srcuserid)
	{
		$hasVoted = SHasUserVoted($subid, $srcuserid);
		if($hasVoted == null) //Never voted this post EVER
		{
			//Create a row about downvoting
			$query = "INSERT INTO votes (userid, sub_id, thumbedup) VALUES ('" . PrepSQL($srcuserid) . "', '" . PrepSQL($subid) . "', False)";					
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE submissions SET sub_rating = sub_rating - 1 WHERE sub_id='" . PrepSQL($subid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif($hasVoted == 'del') //Wants to reupvote
		{
			//Mark the vote as downvoted + uNdeleted
			$query = "UPDATE votes SET deleted = 0, thumbedup = False WHERE sub_id='" . PrepSQL($subid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE submissions SET sub_rating = sub_rating - 1 WHERE sub_id='" . PrepSQL($subid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif($hasVoted) //Is currently upvoted already
		{
			//Downvote twice to undo the upvote
			$query = "UPDATE votes SET deleted = 0, thumbedup = False WHERE sub_id='" . PrepSQL($subid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE submissions SET sub_rating = sub_rating - 2 WHERE sub_id='" . PrepSQL($subid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif(!$hasVoted) //Is currently downvoted already
		{
			//Mark the vote as deleted
			$query = "UPDATE votes SET deleted = 1, thumbedup = False WHERE sub_id='" . PrepSQL($subid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE submissions SET sub_rating = sub_rating + 1 WHERE sub_id='" . PrepSQL($subid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}else
		{
			$pagecontent .= Error('Big fuckup! AAAAAAAAA');
		}
	}
	//VoteCount
	function SGetVotes($subid)
	{
		$query = "SELECT sub_rating FROM submissions WHERE sub_deleted='0' AND sub_id='" . PrepSQL($subid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['sub_rating'];
	}
	CheckSubmission();
?>