<?php
	function PageGameCount($pageid)
	{
		$query = "SELECT COUNT(*) FROM gamelist WHERE deleted='0' AND pageid='" . PrepSQL($pageid) . "'";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function GameCount()
	{
		$query = "SELECT COUNT(*) FROM gamelist";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function GameExists($gameid)
	{
		$query = "SELECT COUNT(*) FROM gamelist WHERE deleted='0' AND gameid='" . PrepSQL($gameid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['COUNT(*)'];
	}
	function CheckGame()
	{
		if(isset($_GET["gameid"]) && is_numeric($_GET["gameid"]))
		{	
			$GLOBALS['gameid'] = floor($_GET["gameid"]);
			if(!GameExists($GLOBALS['gameid']))
			{
				$GLOBALS['gameid'] = null;
				$GLOBALS['pageid'] = 404;
			}
		}else
		{
			$GLOBALS['gameid'] = null;
		}
	}
	function GetGameNameFromID($tmp_gameid)
	{
		$query = "SELECT gametitle FROM gamelist WHERE deleted='0' AND gameid='" . PrepSQL($tmp_gameid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row['gametitle'];
	}
	function GetGameFromID($tmp_gameid)
	{
		$query = "SELECT * FROM gamelist WHERE deleted='0' AND gameid='" . PrepSQL($tmp_gameid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	//Used for game suggestions and shit
	Function CreateLetterIndex($Title)
	{
		$FinalTitle = strtoupper(substr($Title, 0, 1));
		if(preg_match('/[A-Z]/', $FinalTitle))
		{
			return $FinalTitle;
		}else
		{
			return '#';
		}
	}
	function DisplayGames($pageid, $letterid)
	{
		$query = "SELECT gameid,gametitle FROM gamelist WHERE pageid='" . $pageid . "' AND gameindex='" . $letterid . "' AND deleted=0 ORDER BY gametitle";
		$result = mysqli_query($GLOBALS['con'],$query);	
		$pagecontent = null;
		$exists = null;
		while($row = mysqli_fetch_assoc($result))
		{
			//Collections mini hack, adds bigger spacing
			if($letterid == '.')
			{
				$pagecontent .= '<br><a href="./index.php?gameid=' . $row['gameid'] . '&pttl=' . GenTitle($row['gametitle']) . '" id="title_' . $row['gameid'] . '">' . $row['gametitle'] . ' (' . GameSubmissionCount($row['gameid']) . ')</a><br><br>';
			}else
			{
				$pagecontent .= '<a href="./index.php?gameid=' . $row['gameid'] . '&pttl=' . GenTitle($row['gametitle']) . '" id="title_' . $row['gameid'] . '">' . $row['gametitle'] . ' (' . GameSubmissionCount($row['gameid']) . ')</a><br>';
			}
			$exists = true;
		}
		if(!isset($exists) && $letterid != '.')
		{
			$pagecontent .= '<span class="CommentDV">No games or software have been submitted under this index.</span><br>';
		}
		return $pagecontent;
	}
	//Renders the box the lets you suggest a game
	function RenderGameSuggestionBox($pretitle, $predate, $precontent)
	{
		$pagecontent = '<form class="SSky_Form" action="./suggest.php?pageid=' . $GLOBALS['pageid'] . '&submit=1" method="post">';	
		$pagecontent .= '<b> Game Title (Max 64 chars): </b><input type="text" maxlength="64" name="gametitle" value="' . $pretitle . '"><br>';
		$pagecontent .= '<b title="E.g. 5th September, 1999"> Game Release Date (Max 50 chars): </b><input type="text" maxlength="50" name="gamedate" value="' . $predate . '"><br>';
		$pagecontent .= '<b> Game Description (Max 5000 chars): </b><br><textarea rows="16" maxlength="5000" id="desc" name="content">' . $precontent . '</textarea><br>';
		$pagecontent .= '<input type="submit" value="Suggest Game">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	function DrawGameHeader()
	{
		$pagecontent = '<center>';
		$pagecontent .= '<h2>Table of Contents</h2>';
		$pagecontent .= '<span class="GameTitle">';
		$pagecontent .= '<a href="#game_0">#</a> ';
		$pagecontent .= '<a href="#game_A">A</a> ';
		$pagecontent .= '<a href="#game_B">B</a> ';
		$pagecontent .= '<a href="#game_C">C</a> ';
		$pagecontent .= '<a href="#game_D">D</a> ';
		$pagecontent .= '<a href="#game_E">E</a> ';
		$pagecontent .= '<a href="#game_F">F</a> ';
		$pagecontent .= '<a href="#game_G">G</a> ';
		$pagecontent .= '<a href="#game_H">H</a> ';
		$pagecontent .= '<a href="#game_I">I</a> ';
		$pagecontent .= '<a href="#game_J">J</a> ';
		$pagecontent .= '<a href="#game_K">K</a> ';
		$pagecontent .= '<a href="#game_L">L</a> ';
		$pagecontent .= '<a href="#game_M">M</a> ';
		$pagecontent .= '<a href="#game_N">N</a> ';
		$pagecontent .= '<a href="#game_O">O</a> ';
		$pagecontent .= '<a href="#game_P">P</a> ';
		$pagecontent .= '<a href="#game_Q">Q</a> ';
		$pagecontent .= '<a href="#game_R">R</a> ';
		$pagecontent .= '<a href="#game_S">S</a> ';
		$pagecontent .= '<a href="#game_T">T</a> ';
		$pagecontent .= '<a href="#game_U">U</a> ';
		$pagecontent .= '<a href="#game_V">V</a> ';
		$pagecontent .= '<a href="#game_W">W</a> ';
		$pagecontent .= '<a href="#game_X">X</a> ';
		$pagecontent .= '<a href="#game_Y">Y</a> ';
		$pagecontent .= '<a href="#game_Z">Z</a> ';
		$pagecontent .= '</span><hr>';
		$pagecontent .= '<h3><i>Missing a title?</i></h3> <a href="./suggest.php?pageid=' . $GLOBALS['pageid'] . '" class="Button">Suggest one here!</a><br>';
		
		//Do the actual contents
		//$pagecontent .= '<br>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], '.');
		//$pagecontent .= '<br>';
		$pagecontent .= '<h1 id="game_0">#</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], '#');
		$pagecontent .= '<h1 id="game_A">A</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'A');
		$pagecontent .= '<h1 id="game_B">B</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'B');
		$pagecontent .= '<h1 id="game_C">C</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'C');
		$pagecontent .= '<h1 id="game_D">D</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'D');
		$pagecontent .= '<h1 id="game_E">E</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'E');
		$pagecontent .= '<h1 id="game_F">F</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'F');
		$pagecontent .= '<h1 id="game_G">G</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'G');
		$pagecontent .= '<h1 id="game_H">H</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'H');
		$pagecontent .= '<h1 id="game_I">I</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'I');
		$pagecontent .= '<h1 id="game_J">J</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'J');
		$pagecontent .= '<h1 id="game_K">K</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'K');
		$pagecontent .= '<h1 id="game_L">L</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'L');
		$pagecontent .= '<h1 id="game_M">M</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'M');
		$pagecontent .= '<h1 id="game_N">N</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'N');
		$pagecontent .= '<h1 id="game_O">O</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'O');
		$pagecontent .= '<h1 id="game_P">P</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'P');
		$pagecontent .= '<h1 id="game_Q">Q</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'Q');
		$pagecontent .= '<h1 id="game_R">R</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'R');
		$pagecontent .= '<h1 id="game_S">S</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'S');
		$pagecontent .= '<h1 id="game_T">T</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'T');
		$pagecontent .= '<h1 id="game_U">U</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'U');
		$pagecontent .= '<h1 id="game_V">V</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'V');
		$pagecontent .= '<h1 id="game_W">W</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'W');
		$pagecontent .= '<h1 id="game_X">X</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'X');
		$pagecontent .= '<h1 id="game_Z">Z</h1>';
		$pagecontent .= DisplayGames($GLOBALS['pageid'], 'Z');
		$pagecontent .= '</center>';
		return $pagecontent;
	}
	CheckGame();
?>