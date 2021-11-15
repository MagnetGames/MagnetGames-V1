<?php 
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	$DEF_Title = 'Post Submission';
	$DEF_Desc = 'Post a Submission to MagnetGames.';
	//Main content is here on the page
	$pagecontent .= PageBegin();

	//Check functions
	function IsMagnet($magnetURI)
	{
		if(strtolower(substr($magnetURI, 0, 15)) == 'magnet:?xt=urn:')
		{
			return true;
		}else
		{
			return false;
		}
	}
	function IsIPFS($magnetURI)
	{
		$sub_link = str_replace("/", "", $magnetURI);

		if(substr($sub_link, 0, 2) == 'Qm' && strlen($sub_link) == 46)
		{
			return true;
		//https://ipfs.io/ipfs/
		}elseif(substr($sub_link, 17, 2) == 'Qm' && strlen($sub_link) == 63)
		{
			return true;
		//http://ipfs.io/ipfs/
		}elseif(substr($sub_link, 16, 2) == 'Qm' && strlen($sub_link) == 62)
		{
			return true;
		}else
		{
			return false;
		}
	}
	$ExampleMagnet = 'magnet:?xt=urn:btih:f7796dfff2dc6a2153d789eaee71a68cda272fb4';
	$ExampleHash = 'QmUmghzduG1CvbI3HoSVwctvuyxVYIy8vycuYYUyxsu8vd';
	function Alert_SubmissionExample($ExampleMagnet, $ExampleHash)
	{
		$content = '<img src="./cssimg/magnet.png" title="Magnet Link"> Magnet Link Example: <b>' . $ExampleMagnet . '</b>
		<br><img src="./cssimg/ipfs.png" title="IPFS Hash"> IPFS Example hash: <b>' . $ExampleHash . '</b>
		<br>It would be wise to add the file extension (such as .7z) in the description/title for IPFS submissions,
		<br>as IPFS does not store the extension (unless it links to a directory).
		<br><br>Make sure that your submission is related to the title in some way.
		<br><i><b>Submissions not relating to the title will be either deleted or moved.</b></i><br>';
		$content .= "<br>If you're unsure on how to use any these" . ', you can check out our <a href="./index.php?pageid=600" target="_blank">guides</a> on <a href="./index.php?pageid=605" target="_blank"><img src="./cssimg/ipfs.png" title="IPFS Hash"> <b>IPFS</b></a> and <a href="./index.php?pageid=604" target="_blank"><img src="./cssimg/magnet.png" title="Magnet Link"> <b>Magnet Links</b></a>.';
		return Alert($content);
	}
	//Are you logged in?
	if(isset($_SESSION['session_userid']))
	{
		//Check if the gameid exists first
		CheckGame();
		if(isset($GLOBALS['gameid']))
		{
			//Is everything set?
			if(isset($_POST["content"]) && isset($_POST["subtitle"]) && isset($_POST["sublink"]))
			{
				$sub_title = htmlspecialchars(substr($_POST["subtitle"], 0, 64)); 
				$sub_desc = htmlspecialchars(substr($_POST["content"], 0, 5000)); 
				$sub_link = htmlspecialchars(substr($_POST["sublink"], 0, 5000)); 
				if(!ContainsText($sub_title) || !ContainsText($sub_desc) || !ContainsText($sub_link)) //Checks if the only thing posted is spaces
				{
					$tmp_game = GetGameFromID($GLOBALS['gameid']);
					$pagecontent .= '<center><img src="./cssimg/page_' . $tmp_game['pageid'] . '.gif"><h1>Creating submission for <a href="./index.php?gameid=' . $GLOBALS['gameid'] . '">' . $tmp_game['gametitle'] . '</a></h1></center><hr>';
					$pagecontent .= Error("Please fill in all the fields with valid data.");
					$pagecontent .= Alert_SubmissionExample($ExampleMagnet, $ExampleHash);
					$pagecontent .= RenderSubmissionBox($sub_title, $sub_desc, $sub_link);
					$pagecontent .= GenGoBack();
				}elseif(GetLastPostTime($_SESSION['session_userid']) >= (time() - 30))
				{
					$tmp_game = GetGameFromID($GLOBALS['gameid']);
					$pagecontent .= '<center><img src="./cssimg/page_' . $tmp_game['pageid'] . '.gif"><h1>Creating submission for <a href="./index.php?gameid=' . $GLOBALS['gameid'] . '">' . $tmp_game['gametitle'] . '</a></h1></center><hr>';
					$pagecontent .= Error("Please wait 30 seconds before posting again.");
					$pagecontent .= Alert_SubmissionExample($ExampleMagnet, $ExampleHash);
					$pagecontent .= RenderSubmissionBox($sub_title, $sub_desc, $sub_link);
					$pagecontent .= GenGoBack();
				}else //Success?
				{
					//Magnet Submissions
					if($sub_link != strtolower($ExampleMagnet) && IsMagnet($sub_link))
					{
						$submissionid = SubmissionCount();
						if(isset($_POST["subanon"]) && $_POST["subanon"])
						{
							$userid = 0; //0 = anonymous user id
						}else
						{
							$userid = $_SESSION['session_userid'];
						}
						
						$query = "INSERT INTO submissions (sub_id, sub_gameid, sub_rating, sub_content, sub_title, sub_link, sub_date, sub_userid, sub_deleted, sub_isipfs) VALUES ('" . PrepSQL($submissionid) . "', '" . PrepSQL($GLOBALS['gameid']) . "', '0', '" . PrepSQL($sub_desc) . "', '" . PrepSQL($sub_title) . "', '" . PrepSQL($sub_link) . "', '" . time() . "', '" . PrepSQL($userid) . "', '0', '0')";
						$result = mysqli_query($GLOBALS['con'], $query);
						$pagecontent .= '<center><h3>Magnet Link Submission #' . $submissionid . ' posted successfully!</h3></center>';
						$pagecontent .= DrawSubmissionFromID($submissionid, true, true);
						//Redirect
						$pagecontent .= '<center><b>Redirecting...</b></center><br>';
						$pagecontent .= '<meta http-equiv="Refresh" content="2; url=./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&pttl=' . GenTitle($sub_title) . '#sub_' . $submissionid . '">';
						//Anti-SPam
						SetLastPostTime($_SESSION['session_userid']);
					//IPFS submissions
					}elseif($sub_link != $ExampleHash && IsIPFS($sub_link))
					{
						$submissionid = SubmissionCount();
						if(isset($_POST["subanon"]) && $_POST["subanon"])
						{
							$userid = 0; //0 = anonymous user id
						}else
						{
							$userid = $_SESSION['session_userid'];
						}
						//Crop the URL out of it (if any), and remove backslashes
						$sub_link = str_replace("/", "", $sub_link);
						$sub_link = substr($sub_link, -46);	
						
						
						$query = "INSERT INTO submissions (sub_id, sub_gameid, sub_rating, sub_content, sub_title, sub_link, sub_date, sub_userid, sub_deleted, sub_isipfs) VALUES ('" . PrepSQL($submissionid) . "', '" . PrepSQL($GLOBALS['gameid']) . "', '0', '" . PrepSQL($sub_desc) . "', '" . PrepSQL($sub_title) . "', '" . PrepSQL($sub_link) . "', '" . time() . "', '" . PrepSQL($userid) . "', '0', '1')";
						$result = mysqli_query($GLOBALS['con'], $query);
						$pagecontent .= '<center><h3>IPFS Submission #' . $submissionid . ' posted successfully!</h3></center>';
						$pagecontent .= DrawSubmissionFromID($submissionid, true, true);
						//Redirect
						$pagecontent .= '<center><b>Redirecting...</b></center><br>';
						$pagecontent .= '<meta http-equiv="Refresh" content="2; url=./viewsubmissions.php?gameid=' . $GLOBALS['gameid'] . '&pttl=' . GenTitle($sub_title) . '#sub_' . $submissionid . '">';
						//Anti-SPam
						SetLastPostTime($_SESSION['session_userid']);
					}else
					{
						if($sub_link == $ExampleMagnet || $sub_link == $ExampleHash)
						{
							$tmp_game = GetGameFromID($GLOBALS['gameid']);
							$pagecontent .= '<center><img src="./cssimg/page_' . $tmp_game['pageid'] . '.gif"><h1>Creating submission for <a href="./index.php?gameid=' . $GLOBALS['gameid'] . '">' . $tmp_game['gametitle'] . '</a></h1></center><hr>';
							$pagecontent .= Error('Please do not submit the examples...');
							$pagecontent .= Alert_SubmissionExample($ExampleMagnet, $ExampleHash);
							$pagecontent .= RenderSubmissionBox($sub_title, $sub_desc, null);
							$pagecontent .= GenGoBack();
						}else
						{
							$tmp_game = GetGameFromID($GLOBALS['gameid']);
							$pagecontent .= '<center><img src="./cssimg/page_' . $tmp_game['pageid'] . '.gif"><h1>Creating submission for <a href="./index.php?gameid=' . $GLOBALS['gameid'] . '">' . $tmp_game['gametitle'] . '</a></h1></center><hr>';
							$pagecontent .= Error('Invalid Magnet Link/IPFS hash!<br><img src="./cssimg/magnet.png" title="Magnet Link"> Magnet Link Example: <b>' . $ExampleMagnet . '</b><br><img src="./cssimg/ipfs.png" title="IPFS Hash"> IPFS Example hash: <b>' . $ExampleHash . '</b>');
							$pagecontent .= RenderSubmissionBox($sub_title, $sub_desc, $sub_link);
							$pagecontent .= GenGoBack();
						}
					}
				}
			}else
			{
				if(isset($_GET["submit"]) && $_GET["submit"])
				{
					$pagecontent .= Error('Missing POST request!');
				}
				$tmp_game = GetGameFromID($GLOBALS['gameid']);
				$pagecontent .= '<center><img src="./cssimg/page_' . $tmp_game['pageid'] . '.gif"><h1>Creating submission for <a href="./index.php?gameid=' . $GLOBALS['gameid'] . '">' . $tmp_game['gametitle'] . '</a></h1></center><hr>';
				$pagecontent .= Alert_SubmissionExample($ExampleMagnet, $ExampleHash);
				$pagecontent .= RenderSubmissionBox(null, null, null);
				$pagecontent .= GenGoBack();
			}
		}else
		{
			$pagecontent .= Error("Invalid GameID!");
			$pagecontent .= GenGoBack();
		}
	}else
	{
		$pagecontent .= Error("You need to be logged in to post submissions!");
		$pagecontent .= GenGoBack();
	}
	//Small but cool meta tag
	if(isset($tmp_game))
	{
		$DEF_Desc = "Post a Submission to '" . $tmp_game['gametitle'] . "'.";
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