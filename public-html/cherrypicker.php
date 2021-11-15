<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/captcha.php';
	include './src/submissions.php';
	$pagecontent .= PageBegin();	
	
	//Stolen function
	function checkExternalFile($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_exec($ch);
		$retCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return $retCode;
	}
	//2021 dirty seedbox cancelation code
	http_response_code(404);
	return;
	//end of cancel code
	
	if(isset($_GET["subid"]) && is_numeric($_GET["subid"]) && SubmissionExists($_GET["subid"]))
	{
		$submission = GetSubmissionFromPostID($_GET["subid"]);
		if(!isset($_SESSION['captcha_locked']))
		{
			GenLock();
		}
		if($_SESSION['captcha_locked'])
		{			
			$DEF_Title = 'Lock Puzzle';
			$DEF_Desc = 'Please do the Puzzle before using "<img src="./cssimg/cherrypick.gif" title="Cherry-picked Magnet Submission"> Cherry-picked Magnet" Submissions!';		
			$pagecontent .= '<center><h1>' . $submission['sub_title'] . '</h1>' . $DEF_Desc . '</center><hr>';		
			//Clicked continue without puzzle
			if(isset($_GET["showdialog"]) && $_GET["showdialog"] == 1)
			{
				GenLock();
				$pagecontent .= Error('Please complete the puzzle!');
			}
			$pagecontent .= DrawCaptcha();
			$pagecontent .= '<center><a href="./cherrypicker.php?subid=' . $_GET["subid"] . '&showdialog=1" class="Button">Continue!</a></center><br>';
			//$pagecontent .= GenGoBack();
			//End of page content
			$pagecontent .= PageEnd();
			
			
			//Add the header content to the beginning of the page.
			$headercontent = GenMeta($DEF_Title, $DEF_Desc);
			$headercontent .= file_get_contents($DEF_Header);
			$headercontent .= file_get_contents($DEF_Navbar);
			$headercontent .= DrawUserBar();
			//Finalise page
			echo $headercontent . $pagecontent;
		}else //Passed, download
		{
			$seedboxurl = CherryURL();
			
			if(checkExternalFile($seedboxurl . $submission['sub_link']) == 200)
			{			
				$fp = fopen($seedboxurl . $submission['sub_link'], 'rb');		
				foreach (get_headers($seedboxurl . $submission['sub_link']) as $header)
				{
					header($header);
				}
				header('Content-Disposition: attachment; filename="' . basename($submission['sub_link']) . '"');
				fpassthru($fp);
				exit;
			}else
			{
				//Something broke, not found on seedbox?
				http_response_code(500);
				//echo 'OH FUCK' . $seedboxurl . $submission['sub_link'];
			}

		}
	}else //Not Found
	{		
		http_response_code(404);
	}
	


?>