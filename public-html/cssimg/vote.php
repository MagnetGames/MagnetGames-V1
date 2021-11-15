<?php
	include '../src/globals.php';
	include '../src/comments.php';
	include '../src/submissions.php';
	if(isset($_SESSION['session_userid'])) //Logged in?
	{
		//Comments
		if(isset($_POST["postid"]) && is_numeric($_POST["postid"]) && CommentExists($_POST["postid"]))
		{
			$hasVoted = CHasUserVoted($_POST["postid"], $_SESSION['session_userid']);
			//Downvotes
			if(isset($_POST["votetype"]) && strtolower($_POST["votetype"]) == 'downvote')
			{
				CDownvote($_POST["postid"], $_SESSION['session_userid']);
				$NewVotes = CGetVotes($_POST["postid"]);
				if($hasVoted == null || $hasVoted == 'del' || $hasVoted == true)
				{
					echo json_encode(array('success' => 1, 'postid' => $_POST["postid"], 'newvotes' => $NewVotes, 'hasvoted' => true, 'votetype' => 'downvote'));
				}else
				{
					echo json_encode(array('success' => 1, 'postid' => $_POST["postid"], 'newvotes' => $NewVotes, 'hasvoted' => false));
				}
			}else //Upvotes
			{
				CUpvote($_POST["postid"], $_SESSION['session_userid']);
				$NewVotes = CGetVotes($_POST["postid"]);
				if($hasVoted == null || $hasVoted == 'del' || $hasVoted == false)
				{
					echo json_encode(array('success' => 1, 'postid' => $_POST["postid"], 'newvotes' => $NewVotes, 'hasvoted' => true, 'votetype' => 'upvote'));
				}else
				{
					echo json_encode(array('success' => 1, 'postid' => $_POST["postid"], 'newvotes' => $NewVotes, 'hasvoted' => false));
				}				
			}
		//Submissions
		}elseif(isset($_POST["subid"]) && is_numeric($_POST["subid"]) && SubmissionExists($_POST["subid"]))
		{
			$hasVoted = SHasUserVoted($_POST["subid"], $_SESSION['session_userid']);
			//Downvotes
			if(isset($_POST["votetype"]) && strtolower($_POST["votetype"]) == 'downvote')
			{
				SDownvote($_POST["subid"], $_SESSION['session_userid']);
				$NewVotes = SGetVotes($_POST["subid"]);
				if($hasVoted == null || $hasVoted == 'del' || $hasVoted == true)
				{
					echo json_encode(array('success' => 1, 'subid' => $_POST["subid"], 'newvotes' => $NewVotes, 'hasvoted' => true, 'votetype' => 'downvote'));
				}else
				{
					echo json_encode(array('success' => 1, 'subid' => $_POST["subid"], 'newvotes' => $NewVotes, 'hasvoted' => false));
				}
			}else //Upvotes
			{
				SUpvote($_POST["subid"], $_SESSION['session_userid']);
				$NewVotes = SGetVotes($_POST["subid"]);
				if($hasVoted == null || $hasVoted == 'del' || $hasVoted == false)
				{
					echo json_encode(array('success' => 1, 'subid' => $_POST["subid"], 'newvotes' => $NewVotes, 'hasvoted' => true, 'votetype' => 'upvote'));
				}else
				{
					echo json_encode(array('success' => 1, 'subid' => $_POST["subid"], 'newvotes' => $NewVotes, 'hasvoted' => false));
				}				
			}
		}else
		{
			echo json_encode(array('success' => 0));
		}
	}else
	{
		echo json_encode(array('success' => 0));
	}
?>