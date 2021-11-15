<?php
    include 'dev_class.igdb.php';
	include 'globals.php';
	include 'gamelist.php';
	include 'submissions.php';
	
	function Blitz_Mid($text, $num_a, $num_b)
	{
		return substr($text, $num_a - 1, $num_b);
	}
	function Blitz_Left($text, $num)
	{
		return Blitz_Mid($text, 1, $num);
	}
	function Blitz_Right($text, $num)
	{
		return Blitz_Mid($text, strlen($text) - $num + 1, $num);
	}
	function ROM_StripTags($text)
	{
		//Remove .ZIP extension
		$text = str_ireplace(".zip", "", $text);
		
		//Don't count anything after '(', also delete empty spaces at the end
		$textarray = explode("(", $text);
		while(Blitz_Right($textarray[0], 1) == " ")
		{
			$textarray[0] = Blitz_Left($textarray[0], strlen($textarray[0])-1);
		}
		//Fix the stupid "Epic Quest, The" shit
		if(Blitz_Right($textarray[0], 5) == ", The")
		{
			$textarray[0] = "The " . Blitz_Left($textarray[0], strlen($textarray[0]) - 5);
		//Do the same but with ", A"	
		}elseif(Blitz_Right($textarray[0], 3) == ", A")
		{
			$textarray[0] = "A " . Blitz_Left($textarray[0], strlen($textarray[0]) - 3);
		}
		return $textarray[0];
	}
	
	//Uses a database API to automatically get info on a rom
	function ROM_GetMeta($gametitle, $platform)
	{
		$gametitle = ROM_StripTags($gametitle);
		$IGDB = new IGDB('8b6c4cbc0863138a907d943903a2c0ef');
		$options = array(
			'fields' => array(
				'id',
				'name', 
				'first_release_date',
				'rating',
				'summary',
				'cover.url',
				'alternative_names.name'
			),
			'search' => $gametitle,
			//'limit' => 1,
			'where' => array(
				'field' => 'release_dates.platform',   // filtering by the platform field
				'postfix' => '=',                      // equals postfix
				'value' => $platform //18                           // looking for platforms with the ID equals to 8
			)
		);
		//Format it
		$result = $IGDB->game($options);
		
		
		//Cause IGDB has a shitty search when it comes to finding the first game in the series, check if title matches 100% first
		$i = 0; //Default as 0 if nothing is found
		while($i < count($result)) 
		{
			if(strtolower($result[$i]->name) == strtolower($gametitle))
			{
				//echo 'ladies and gentleman... WE GOT EM';
				break;
			}			
			$i++;
		}
		//Nothing found after all that?
		if($i == count($result))
		{
			$i = 0;
		}
		
		//Null returns
		if(!isset($result[$i]->name))
			return null;
		
		if(!isset($result[$i]->summary))
			return null;
		
		
		//Export mains data
		$dump["name"] = $result[$i]->name;
		
		//Does a description exist?
		if(isset($result[$i]->summary))
		{
			$dump["desc"] = substr($result[$i]->summary, 0, 5000);
		}else
		{
			$dump["desc"] = 'TODO: Add description'; //Default to this
		}
		$dump["date"] = date('jS F, Y', $result[$i]->first_release_date);
		if(isset($result[$i]->cover))
		{
			$dump["cover"] = htmlspecialchars('http:' . str_replace('/t_thumb/', '/t_cover_big/', $result[$i]->cover->url));
		}else
		{
			$dump["cover"] = null;
		}
		
		//Does a rating exist?
		if(isset($result[$i]->rating))
		{
			$dump["rating"] = round($result[$i]->rating) / 20;
		}else
		{
			$dump["rating"] = 2.5; //Default to 2.5
		}
		
		//Letter index
		if(!preg_match('/[^A-Z]/', strtoupper(Blitz_Left($dump["name"],1))))
		{
			$dump["letterindex"] = Blitz_Left(strtoupper($dump["name"]),1);
		}else
		{
			$dump["letterindex"] = '#';
		}
		
		//Alt titles (TOo much info on most games)
	//	for($x = 0; $x < count($result[$i]->alternative_names); $x++)
	//	{
	//		$dump["name"] = $dump["name"] . ' / ' . $result[$i]->alternative_names[$x]->name;
	//	}
		
		//Debug
		//echo json_encode($result);
		
		//Return the info
		return $dump;
	}
	function GameTitleExists($gametitle, $pageid)
	{
		$query = "SELECT * FROM gamelist WHERE deleted='0' AND gametitle='" . PrepSQL($gametitle) . "' AND pageid='" . PrepSQL($pageid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		return $row;
	}
	
	function ROM_Automate($ROM_FileName)
	{
		$ROM_PageID = 11;
		$ROM_IGDBConsoleID = 18;
		$ROM_BaseSubmission = 43; //Base SubID
		$ROM_Path = '%5bNo-Intro%5d%20Nintendo%20Entertainment%20System%20(17-05-2018)/';
		
		$game = ROM_GetMeta($ROM_FileName, $ROM_IGDBConsoleID);
		if(isset($game))
		{
			
			echo $game["name"].'<br>';
			echo $game["rating"] . ' Stars'.'<br>';
			echo $game["date"] .'<br>';
			echo $game["desc"] .'<br>';
			echo $game["letterindex"] .'<br>';
			echo $game["cover"] .'<br>';

			$GetGame = GameTitleExists($game["name"], $ROM_PageID);
			//Our game is already in our MG table!
			if(isset($GetGame))
			{
				$GameID = $GetGame['gameid'];
			}else
			{
				$GameID = GameCount();
				$query = "INSERT INTO gamelist (pageid, gametitle, gamedate, gamedesc, gameindex, gameid, deleted, gamerating, gamecover) VALUES ('" . PrepSQL($ROM_PageID) . "', '" . PrepSQL(htmlspecialchars($game["name"])) . "', '" . PrepSQL(htmlspecialchars($game["date"])) . "', '"  . PrepSQL(htmlspecialchars($game["desc"])) . "', '" . PrepSQL($game["letterindex"]) . "', '" . PrepSQL($GameID) . "', '0', '" . PrepSQL($game["rating"]) . "', '" . PrepSQL($game["cover"]) . "')";
				$result = mysqli_query($GLOBALS['con'], $query);
			}
			
			//Now make the cherry submission
			$seedboxurl = CherryURL();		
			$submissionid = SubmissionCount();
			$query = "INSERT INTO submissions (sub_id, sub_gameid, sub_rating, sub_content, sub_title, sub_link, sub_date, sub_userid, sub_deleted, sub_isipfs, sub_basesub) VALUES ('" . PrepSQL($submissionid) . "', '" . PrepSQL($GameID) . "', '0', '" . PrepSQL(htmlspecialchars('[No-Intro] ' . str_ireplace(".zip", "", $ROM_FileName))) . "', '" . PrepSQL(htmlspecialchars($ROM_FileName)) . "', '" . PrepSQL(htmlspecialchars($ROM_Path . rawurlencode($ROM_FileName))) . "', '" . time() . "', '0', '0', '2', '" . PrepSQL($ROM_BaseSubmission) . "')";
			$result = mysqli_query($GLOBALS['con'], $query);
			echo $query.'<br>';
		}else
		{
			echo 'Error with getting the rom: ' . $ROM_FileName . '<br>';
		}
	}
	
	$contents = file('romnames.txt');
	ini_set('max_execution_time', 6000);
	set_time_limit(6000);
	foreach($contents as $line) 
	{
		ROM_Automate(str_ireplace("\r\n", '', $line));
	}
	
?>