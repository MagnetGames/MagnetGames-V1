<?php
	function PageCommentCount($pageid, $pagetype)
	{
		$query = "SELECT COUNT(*) FROM comments WHERE com_deleted='0' AND com_" . $pagetype . "='" . PrepSQL($pageid) . "'";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function SubCommentCount($subid)
	{
		$query = "SELECT COUNT(*) FROM comments WHERE com_deleted='0' AND com_subid='" . PrepSQL($subid) . "'";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function CommentCount()
	{
		$query = "SELECT COUNT(*) FROM comments LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function CommentExists($tmp_postid)
	{
		$query = "SELECT COUNT(*) FROM comments WHERE com_deleted='0' AND com_id='" . PrepSQL($tmp_postid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	//Just basic on the fly comment headers etc
	function CommentsBegin()
	{
		$pagecontent = '<div id="comments">';
			$pagecontent .= '<div>';
				$pagecontent .= '<h2>Comments:</h2>';
				$pagecontent .= '<hr>';
			$pagecontent .= '</div>';	
		return $pagecontent;
	}
	function CommentsEnd()
	{
		return '</div>';
	}
	function GetCommentFromPostID($tmp_postid)
	{
		$query = "SELECT * FROM comments WHERE com_deleted='0' AND com_id='" . PrepSQL($tmp_postid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	function GetUserIDFromPostID($tmp_postid)
	{
		$query = "SELECT com_userid FROM comments WHERE com_deleted='0' AND com_id='" . PrepSQL($tmp_postid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['com_userid'];
	}
	//Renders the box the lets you post a comment
	function RenderCommentBox($precontent, $pagetype, $replyid)
	{
		$pagecontent = null;
		if(isset($_SESSION['session_userid']))
		{
			if(isset($replyid) && is_numeric($replyid) && CommentExists($replyid))
			{
				$pagecontent .= '<form class="SAqua_Form" action="./createcomment.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '&replyid='  . $replyid . '" method="post">';					
			}else
			{
				$pagecontent .= '<form class="SAqua_Form" action="./createcomment.php?' . $pagetype . '=' . $GLOBALS[$pagetype] . '" method="post">';	
			}
			if(isset($precontent))
			{
				$pagecontent .= '<b> Comment (Max 2000 chars): </b><br><textarea rows="8" maxlength="2000" id="text" name="content">' . $precontent . '</textarea><br>';
			}else
			{
				$pagecontent .= '<b> Comment (Max 2000 chars): </b><br><textarea rows="8" maxlength="2000" id="text" name="content"></textarea><br>';
			}
			$pagecontent .= '<input type="submit" value="Post Comment">';
			$pagecontent .= '</form>';
		}else
		{
			$pagecontent .= '<form class="SAqua_Form">';	
			$pagecontent .= '<b> Comment (Max 2000 chars): </b><br><textarea rows="8" maxlength="2000" id="text" name="content" disabled>You need to be logged in to post comments.</textarea><br>';
			$pagecontent .= '<input type="submit" value="Post Comment" disabled>';
			$pagecontent .= '</form>';
		}
		return $pagecontent;
	}
	function RenderComment($com_userid, $com_date, $com_content, $com_votes, $com_id, $com_replyid, $arg_style, $arg_drawreply, $arg_drawreport) 
	{
		$com_user = GetUserFromID($com_userid);
		$com_username = $com_user['username'];
		//Badges
		$com_badges = RenderBadges($com_user);
		
		$date = getdate($com_date);
		//Is there a reply?
		if(isset($com_replyid))
		{
			//Did it get deleted?
			if(CommentExists($com_replyid))
			{
				$tmp_reply = GetCommentFromPostID($com_replyid);
				$reply_content = $tmp_reply['com_content'];
				$reply_user = GetUserFromID(GetUserIDFromPostID($tmp_reply['com_id']));
				$reply_username = $reply_user['username'];				
			}else
			{
				$reply_content = '<span class="CommentDV"><b><i>This comment no longer exists!</i></b></span>';
				$reply_username = 'Anonymous';
			}			
		}
		
		$pagecontent = '<div class="' . $arg_style . 'Comment" id="com_' . $com_id . '">';
			$pagecontent .= '<div>';
				$pagecontent .= '<div class="' . $arg_style . 'CommentTitle">';
					$pagecontent .= '<b><center>';
						//Avatar
						$pagecontent .= '<a href="./profile.php?username=' . $com_username . '" title="View Profile!">' . get_gravatar($com_user['useremail'], 48, $arg_style . 'CAvatar') . '</a><br>';
						$pagecontent .= $com_badges . ' <a href="./profile.php?username=' . $com_username . '" title="View Profile!">' . $com_username . '</a> </b> <a href="./viewcomment.php?postid=' . $com_id . '" target="_blank" title="View Comment from Post ID">#' . $com_id . '</a> <b> - <span title="UTC - DD/MM/YYYY">(' . TimeToString($date['hours'], $date['minutes']) . ' - ' . $date['mday']. '/' . $date['mon']. '/' . $date['year'] . ')</span>';
						if(isset($com_replyid))
						{
							$pagecontent .= ' - <a href="#com_' . $com_replyid . '" title="Scroll to Comment">In reply to ' . $reply_username . '</a></b> <a href="./viewcomment.php?postid=' . $com_replyid . '" target="_blank" title="View Reply from Post ID">#' . $com_replyid . '</a><b>';
						}
					$pagecontent .= '</center></b>';
				$pagecontent .= '</div>';
				if(isset($com_replyid))
				{
					$pagecontent .= '<div class="' . $arg_style . 'CommentTitle">';
					$pagecontent .= '<b><a href="./profile.php?username=' . $reply_username . '" title="View Profile!">' . $reply_username . '</a> said:</b><br><div class="' . $arg_style . 'CommentContent"><pre>' . $reply_content . '</pre></div></div>';
				}
				$pagecontent .= '<div class="CommentContent"><pre>';
					$pagecontent .= $com_content;
				$pagecontent .= '</pre></div>';
			$pagecontent .= '</div>';
			$pagecontent .= '<div class="' . $arg_style . 'CommentRatings">';
				$pagecontent .= '<b>';
					//Is logged in?
					if(isset($_SESSION['session_userid']))
					{
						if($arg_drawreply)
						{
							$pagecontent .= '<a href="./reply.php?postid=' . $com_id . '">Reply To This Comment</a>';
						}
						if($arg_drawreply && $arg_drawreport)
						{
							$pagecontent .= ' - ';
						}
						if($arg_drawreport)
						{
							if($com_userid == $_SESSION['session_userid'])
							{
								$pagecontent .= '<a href="./delete.php?postid=' . $com_id . '">Delete Comment</a>';
							}else
							{
								$pagecontent .= '<a href="./report.php?postid=' . $com_id . '">Report This Comment</a>';
							}
						}
					}else
					{
						if($arg_drawreply)
						{
							$pagecontent .= '<a href="./login.php?showdialog=1">Reply To This Comment</a>';	
						}
						if($arg_drawreply && $arg_drawreport)
						{
							$pagecontent .= ' - ';
						}
						if($arg_drawreport)
						{
							$pagecontent .= '<a href="./login.php?showdialog=1">Report This Comment</a>';				
						}
					}					
					// Upvote/Downvote colours
					if($com_votes > 0)
					{
						$pagecontent .= ' - <span class="CommentUV" id="cvotes_' . $com_id . '">+' . $com_votes . ' </span>';
					}
					elseif($com_votes < 0)
					{
						$pagecontent .= ' - <span class="CommentDV" id="cvotes_' . $com_id . '">' . $com_votes . ' </span>';
					}
					else
					{
						$pagecontent .= ' - <span id="cvotes_' . $com_id . '">0 </span>';
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
						//Never voted
						$HasUserVoted = CHasUserVoted($com_id, $_SESSION['session_userid']);
						if($HasUserVoted == null || $HasUserVoted == 'del')
						{
							$pagecontent .= '<img src="./cssimg/thumb_gup.png" title="Upvote" id="cuv_' . $com_id . '" onclick="CUpvote(' . $com_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_gdown.png" title="Downvote" id="cdv_' . $com_id . '" onclick="CDownvote(' . $com_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}elseif($HasUserVoted) //Has Upvoted
						{
							$pagecontent .= '<img src="./cssimg/thumb_up.png" title="Upvote" id="cuv_' . $com_id . '" onclick="CUpvote(' . $com_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_gdown.png" title="Downvote" id="cdv_' . $com_id . '" onclick="CDownvote(' . $com_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
						}else //Has Downvoted
						{
							$pagecontent .= '<img src="./cssimg/thumb_gup.png" title="Upvote" id="cuv_' . $com_id . '" onclick="CUpvote(' . $com_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
							$pagecontent .= '<img src="./cssimg/thumb_down.png" title="Downvote" id="cdv_' . $com_id . '" onclick="CDownvote(' . $com_id . ')" class="Clickable" ' . $MobileZoomTags . '> ';
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
	function DrawCommentFromID($com_id, $arg_style, $arg_drawreply, $arg_drawreport)
	{
		$query = "SELECT com_id,com_rating,com_content,com_date,com_userid,com_replyid FROM comments WHERE com_deleted='0' AND com_id='" . PrepSQL($com_id) . "'LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		if(isset($row))
		{
			return RenderComment($row['com_userid'], $row['com_date'], $row['com_content'], $row['com_rating'], $row['com_id'], $row['com_replyid'], $arg_style, $arg_drawreply, $arg_drawreport);
		}else
		{
			return Error("PHP: Comment doesn't exist");
		}
	}
	function DrawCommentsFromPage($pageid, $pagetype)
	{
		$pagecontent = null;
		$count = PageCommentCount($pageid, $pagetype);
		//Wait, there's no comments at all?
		if($count == 0)
		{
			$pagecontent .= '<center>There are currently no comments on this page.<br><b>Why not be the first?</b></center>';
		}else
		{
			//Draw the top comment segment
			
			$query = "SELECT com_id,com_rating,com_content,com_date,com_userid,com_replyid FROM comments WHERE com_deleted='0' AND com_" . $pagetype . "='" . PrepSQL($pageid) . "' AND com_rating > 0 ORDER BY com_rating DESC LIMIT 2";
			$result = mysqli_query($GLOBALS['con'],$query);	
			$topcomments = null;
			while($row = mysqli_fetch_assoc($result))
			{
				$topcomments .= RenderComment($row['com_userid'], $row['com_date'], $row['com_content'], $row['com_rating'], $row['com_id'], $row['com_replyid'], 'SLime_', true, true);
			}
			//Only show top comments where votes exist
			if(isset($topcomments))
			{
				$pagecontent .= '<center><h3>Top Comments</h3></center>' . $topcomments;
			}
			//Normal comments
			$pagecontent .= '<center><h3>Recent Comments</h3></center>';
			$query = "SELECT com_id,com_rating,com_content,com_date,com_userid,com_replyid FROM comments WHERE com_deleted='0' AND com_" . $pagetype . "='" . PrepSQL($pageid) . "' ORDER BY com_id DESC LIMIT 10";
			$result = mysqli_query($GLOBALS['con'],$query);
			while($row = mysqli_fetch_assoc($result))
			{
				$pagecontent .= RenderComment($row['com_userid'], $row['com_date'], $row['com_content'], $row['com_rating'], $row['com_id'], $row['com_replyid'], 'SAqua_', true, true);
			}
			
			//Add view all comments link
			if($count >= 10)
			{
				$pagecontent .= '<center>Showing <u>10</u> comments out of <u>' . $count . '</u>.';
			}else
			{
				$pagecontent .= '<center>Showing <u>' . $count . '</u> comments out of <u>' . $count . '</u>.';
			}
			$pagecontent .= '<br><b><a href="./viewcomments.php?' . $pagetype . '=' . $pageid .'&pttl=' . GenTitle(GenTitleForPageID($pageid)) . '">View all comments</a></b></center>';
		}
		return $pagecontent;
	}
	//Marks a comment as deleted
	function DeleteCommentFromID($com_id)
	{
		$query = "UPDATE comments SET com_deleted='1', com_content='Deleted', com_userid='0' WHERE com_id='" . PrepSQL($com_id) . "' LIMIT 1";		
		$result = mysqli_query($GLOBALS['con'],$query);
	}
	//Edits a user's lastpost time 
	function SetLastPostTime($userid)
	{
		$query = "UPDATE users SET lastposted='" . time() ."' WHERE userid='" . PrepSQL($userid) . "' LIMIT 1";		
		$result = mysqli_query($GLOBALS['con'],$query);
	}
	//Gets a user's lastpost time
	function GetLastPostTime($userid)
	{
		$query = "SELECT lastposted FROM users WHERE userid='" . PrepSQL($userid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['lastposted'];
	}
	
	//Votes
	function CHasUserVoted($postid, $srcuserid)
	{
		$query = "SELECT thumbedup,deleted FROM votes WHERE com_id='" . PrepSQL($postid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";
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
	function CUpvote($postid, $srcuserid)
	{
		$hasVoted = CHasUserVoted($postid, $srcuserid);
		if($hasVoted == null) //Never voted this post EVER
		{
			//Create a row about upvoting
			$query = "INSERT INTO votes (userid, com_id, thumbedup) VALUES ('" . PrepSQL($srcuserid) . "', '" . PrepSQL($postid) . "', True)";					
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE comments SET com_rating = com_rating + 1 WHERE com_id='" . PrepSQL($postid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif($hasVoted == 'del') //Wants to reupvote
		{
			//Mark the vote as upvoted + uNdeleted
			$query = "UPDATE votes SET deleted = 0, thumbedup = True WHERE com_id='" . PrepSQL($postid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE comments SET com_rating = com_rating + 1 WHERE com_id='" . PrepSQL($postid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif($hasVoted) //Is currently upvoted already
		{
			//Mark the vote as deleted and take 1 vote
			$query = "UPDATE votes SET deleted = 1, thumbedup = True WHERE com_id='" . PrepSQL($postid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE comments SET com_rating = com_rating - 1 WHERE com_id='" . PrepSQL($postid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif(!$hasVoted) //Is currently downvoted already
		{
			//Upvote twice to undo the downvote
			$query = "UPDATE votes SET deleted = 0, thumbedup = True WHERE com_id='" . PrepSQL($postid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE comments SET com_rating = com_rating + 2 WHERE com_id='" . PrepSQL($postid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}else
		{
		$pagecontent .= Error('Big fuckup! AAAAAAAAA');
		}
	}
	//Downvotes
	function CDownvote($postid, $srcuserid)
	{
		$hasVoted = CHasUserVoted($postid, $srcuserid);
		if($hasVoted == null) //Never voted this post EVER
		{
			//Create a row about downvoting
			$query = "INSERT INTO votes (userid, com_id, thumbedup) VALUES ('" . PrepSQL($srcuserid) . "', '" . PrepSQL($postid) . "', False)";					
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE comments SET com_rating = com_rating - 1 WHERE com_id='" . PrepSQL($postid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif($hasVoted == 'del') //Wants to reupvote
		{
			//Mark the vote as downvoted + uNdeleted
			$query = "UPDATE votes SET deleted = 0, thumbedup = False WHERE com_id='" . PrepSQL($postid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE comments SET com_rating = com_rating - 1 WHERE com_id='" . PrepSQL($postid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif($hasVoted) //Is currently upvoted already
		{
			//Downvote twice to undo the upvote
			$query = "UPDATE votes SET deleted = 0, thumbedup = False WHERE com_id='" . PrepSQL($postid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE comments SET com_rating = com_rating - 2 WHERE com_id='" . PrepSQL($postid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}elseif(!$hasVoted) //Is currently downvoted already
		{
			//Mark the vote as deleted
			$query = "UPDATE votes SET deleted = 1, thumbedup = False WHERE com_id='" . PrepSQL($postid) . "' AND userid='" . PrepSQL($srcuserid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
			$query = "UPDATE comments SET com_rating = com_rating + 1 WHERE com_id='" . PrepSQL($postid) . "' LIMIT 1";		
			$result = mysqli_query($GLOBALS['con'],$query);
		}else
		{
		$pagecontent .= Error('Big fuckup! AAAAAAAAA');
		}
	}
	//VoteCount
	function CGetVotes($postid)
	{
		$query = "SELECT com_rating FROM comments WHERE com_deleted='0' AND com_id='" . PrepSQL($postid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['com_rating'];
	}
	//Ok now this is getting ridiculous
	function GetPageTypeFromComment($postid)
	{
		$query = "SELECT com_pageid,com_gameid,com_subid FROM comments WHERE com_deleted='0' AND com_id='" . PrepSQL($postid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		$pagecontent .= Error($row['com_pageid']);
		$pagecontent .= Error($row['com_gameid']);
		$pagecontent .= Error($row['com_subid']);
		if(isset($row['com_gameid']) && !isset($row['com_subid']))
		{
			return 'gameid';
		}elseif(!isset($row['com_gameid']) && !isset($row['com_subid']))
		{
			return 'pageid';
		}elseif(isset($row['com_subid']))
		{
			return 'subid';
		}
	}
?>