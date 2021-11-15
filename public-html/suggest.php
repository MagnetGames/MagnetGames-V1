<?php 
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	include './src/metadata.php'; //Contains the titles/descriptions of the all html based pages
	$DEF_Title = 'Suggest a game!';
	$DEF_Desc = 'Suggest a game to be listed on MagnetGames!';
	CheckPage();
	//Main content is here on the page
	$pagecontent .= PageBegin();

	function CountSuggestions()
	{
		$query = "SELECT COUNT(*) FROM suggestions";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	
	//Are you logged in?
	if(isset($_SESSION['session_userid']))
	{
		//Check if the pageid exists first
		if(isset($GLOBALS['pageid']) && PageGameCount($GLOBALS['pageid']) > 0)
		{
			//Is everything set?
			if(isset($_POST["content"]) && isset($_POST["gametitle"]) && isset($_POST["gamedate"]))
			{
				$game_title = htmlspecialchars(substr($_POST["gametitle"], 0, 64)); 
				$game_date = htmlspecialchars(substr($_POST["gamedate"], 0, 500)); 
				$game_desc = htmlspecialchars(substr($_POST["content"], 0, 5000)); 
				if(!ContainsText($game_title) || !ContainsText($game_date) || !ContainsText($game_desc)) //Checks if the only thing posted is spaces
				{
					$pagecontent .= '<center><img src="./cssimg/page_' . $GLOBALS['pageid'] . '.gif"><h1>Suggest a game!</h1></center>';
					$pagecontent .= GenGoBack();
					$pagecontent .= '<hr>';
					$pagecontent .= Error("Please fill in all the fields with valid data.");
					$pagecontent .= Alert("<b>Remember to check if the game's already listed before suggesting it!</b><br>Games that do not exist on the platform won't be approved.");
					$pagecontent .= RenderGameSuggestionBox($game_title, $game_date, $game_desc);
				}elseif(GetLastPostTime($_SESSION['session_userid']) >= (time() - 30))
				{
					$pagecontent .= '<center><img src="./cssimg/page_' . $GLOBALS['pageid'] . '.gif"><h1>Suggest a game!</h1></center>';
					$pagecontent .= GenGoBack();
					$pagecontent .= '<hr>';
					$pagecontent .= Error("Please wait 30 seconds before posting again.");
					$pagecontent .= Alert("<b>Remember to check if the game's already listed before suggesting it!</b><br>Games that do not exist on the platform won't be approved.");
					$pagecontent .= RenderGameSuggestionBox($game_title, $game_date, $game_desc);
				}else //Success?
				{
					$suggestid = CountSuggestions();
					$query = "INSERT INTO suggestions (gametitle, gamedate, gamedesc, deleted, pageid, userid, suggestionid) VALUES ('" . PrepSQL($game_title) . "', '" . PrepSQL($game_date) . "', '" . PrepSQL($game_desc) . "', '0', '" . PrepSQL($GLOBALS['pageid']) . "', '" . PrepSQL($_SESSION['session_userid']) . "', '" . PrepSQL($suggestid) . "')";
					$result = mysqli_query($GLOBALS['con'], $query);
					$pagecontent .= Alert('Successfully suggested <b>' . $game_title . "</b>!<br><i>It will appear on the Table of Contents once it's been approved by an Admin</i><br><br><b>Game Release Date: </b>" . $game_date . '<br><b>Game Description: </b><br>' . $game_desc);
					//Anti-SPam
					SetLastPostTime($_SESSION['session_userid']);
					//Redirect
					$pagecontent .= '<center><b>Redirecting...</b></center>';
					$pagecontent .= '<meta http-equiv="Refresh" content="4; url=./index.php?pageid=' . $GLOBALS['pageid'] . '">';
				}
			}else
			{
				if(isset($_GET["submit"]) && $_GET["submit"])
				{
					$pagecontent .= Error('Missing POST request!');
				}
				$pagecontent .= '<center><img src="./cssimg/page_' . $GLOBALS['pageid'] . '.gif"><h1>Suggest a game!</h1></center>';
				$pagecontent .= GenGoBack();
				$pagecontent .= '<hr>';
				$pagecontent .= Alert("<b>Remember to check if the game's already listed before suggesting it!</b><br>Games that do not exist on the platform won't be approved.");
				$pagecontent .= RenderGameSuggestionBox(null, null, null);
			}
		}else
		{
			$pagecontent .= Error("Invalid PageID!");
			$pagecontent .= GenGoBack();
		}
	}else
	{
		$pagecontent .= Error("You need to be logged in to post game suggestions!");
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