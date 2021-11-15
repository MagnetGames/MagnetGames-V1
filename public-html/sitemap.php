<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/gamelist.php';
	include './src/submissions.php';
	include './src/metadata.php'; //Contains the titles/descriptions of the all html based pages
	header('Content-type: text/xml');
	
	//Generic pages
	function GenSitemapFromUrl($pageurl, $priority = '0.5', $lastmod = null)
	{
		$pagecontent = '<url>'."\n";
		$pagecontent .= '	<loc>https://magnet-games.com/' . htmlspecialchars($pageurl) . '</loc>'."\n";
		if(isset($lastmod))
		{
			$pagecontent .= '	<lastmod>' . date('Y-m-d\Th:m:s+00:00', $lastmod) . '</lastmod>'."\n";
		}
		$pagecontent .= '	<priority>' . $priority . '</priority>'."\n";
		$pagecontent .= '</url>'."\n";
		return $pagecontent;
	}
	//EzPageID thingy
	function GenSitemapFromPageID($pageid, $priority = '0.5')
	{
		//Get time from latest comment
		$query = "SELECT * FROM comments WHERE com_deleted='0' AND com_pageid='" . PrepSQL($pageid) . "' LIMIT 1";
		$result = mysqli_query($GLOBALS['con'],$query);
		$row = mysqli_fetch_assoc($result);
		
		if(isset($row['com_date']))
		{
			$lastmod = $row['com_date'];
		}elseif(file_exists('src/pages/page_' . $pageid . '.html'))
		{
			$lastmod = filemtime('src/pages/page_' . $pageid . '.html');
		}else //Failsafe
		{
			$lastmod = null;
		}
		$pagecontent = GenSitemapFromUrl('index.php?pageid=' . $pageid . '&pttl=' . GenTitle(GenTitleForPageID($pageid)), '0.8', $lastmod);
		$pagecontent .= GenSitemapFromUrl('viewcomments.php?pageid=' . $pageid . '&pttl=' . GenTitle(GenTitleForPageID($pageid)), '0.5', $lastmod);
		$pagecontent .= GenSitemapFromUrl('viewcomments.php?pageid=' . $pageid . '&sortby=oldest' . '&pttl=' . GenTitle(GenTitleForPageID($pageid)), '0.3', $lastmod);
		$pagecontent .= GenSitemapFromUrl('viewcomments.php?pageid=' . $pageid . '&sortby=upvotes' . '&pttl=' . GenTitle(GenTitleForPageID($pageid)), '0.3', $lastmod);
		$pagecontent .= GenSitemapFromUrl('viewcomments.php?pageid=' . $pageid . '&sortby=downvotes' . '&pttl=' . GenTitle(GenTitleForPageID($pageid)), '0.3', $lastmod);
		return $pagecontent;
	}
	function GenSitemapFromGames()
	{
		$pagecontent = null;
		$query = "SELECT * FROM gamelist WHERE deleted='0'";
		$result = mysqli_query($GLOBALS['con'],$query);
		while($row = mysqli_fetch_assoc($result))
		{
			$pagecontent .= '<!-- ' . htmlspecialchars($row['gametitle']) . ' -->' . "\n";
			$pagecontent .= GenSitemapFromUrl('index.php?gameid=' . $row['gameid'] . '&pttl=' . GenTitle($row['gametitle']), '0.6');
			$pagecontent .= '<!-- ' . htmlspecialchars($row['gametitle']) . ' Submissions -->' . "\n";
			$pagecontent .= GenSitemapFromUrl('viewsubmissions.php?gameid=' . $row['gameid'] . '&pttl=' . GenTitle($row['gametitle']), '0.6');
			$pagecontent .= GenSitemapFromUrl('viewsubmissions.php?gameid=' . $row['gameid'] . '&sortby=oldest&pttl=' . GenTitle($row['gametitle']), '0.4');
			$pagecontent .= GenSitemapFromUrl('viewsubmissions.php?gameid=' . $row['gameid'] . '&sortby=upvotes&pttl=' . GenTitle($row['gametitle']), '0.4');
			$pagecontent .= GenSitemapFromUrl('viewsubmissions.php?gameid=' . $row['gameid'] . '&sortby=downvotes&pttl=' . GenTitle($row['gametitle']), '0.4');
			$pagecontent .= '<!-- ' . htmlspecialchars($row['gametitle']) . ' Comments -->' . "\n";
			$pagecontent .= GenSitemapFromUrl('viewcomments.php?gameid=' . $row['gameid'] . '&pttl=' . GenTitle($row['gametitle']), '0.5');
			$pagecontent .= GenSitemapFromUrl('viewcomments.php?gameid=' . $row['gameid'] . '&sortby=oldest&pttl=' . GenTitle($row['gametitle']), '0.3');
			$pagecontent .= GenSitemapFromUrl('viewcomments.php?gameid=' . $row['gameid'] . '&sortby=upvotes&pttl=' . GenTitle($row['gametitle']), '0.3');
			$pagecontent .= GenSitemapFromUrl('viewcomments.php?gameid=' . $row['gameid'] . '&sortby=downvotes&pttl=' . GenTitle($row['gametitle']), '0.3');
		}
		return $pagecontent;
	}
	function GenSitemapFromSubmissions()
	{
		$pagecontent = null;
		$query = "SELECT * FROM submissions WHERE sub_deleted='0'";
		$result = mysqli_query($GLOBALS['con'],$query);
		while($row = mysqli_fetch_assoc($result))
		{
			$pagecontent .= '<!-- ' . htmlspecialchars($row['sub_title']) . ' -->' . "\n";
			$pagecontent .= GenSitemapFromUrl('viewsubmission.php?postid=' . $row['sub_id'] . '&pttl=' . GenTitle($row['sub_title']), '0.7', $row['sub_date']);
		}
		return $pagecontent;
	}
	function GenSitemapFromComments()
	{
		$pagecontent = null;
		$query = "SELECT * FROM comments WHERE com_deleted='0'";
		$result = mysqli_query($GLOBALS['con'],$query);
		while($row = mysqli_fetch_assoc($result))
		{
			$pagecontent .= GenSitemapFromUrl('viewcomment.php?postid=' . $row['com_id'], '0.2', $row['com_date']);
		}
		return $pagecontent;
	}
	$pagecontent = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
	$pagecontent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";
	$pagecontent .= '<!-- Home Page -->' . "\n";
	$pagecontent .= GenSitemapFromUrl(null, '1.0', time());
	$pagecontent .= '<!-- Main Pages -->' . "\n";
	$pagecontent .= GenSitemapFromUrl('login.php', '0.9', filemtime('login.php'));
	$pagecontent .= GenSitemapFromUrl('register.php', '0.9', filemtime('register.php'));
	$pagecontent .= GenSitemapFromPageID(600, '0.6'); //Guides
	$pagecontent .= GenSitemapFromPageID(601, '0.9'); //Community Guidelines
	$pagecontent .= GenSitemapFromPageID(602, '0.9'); //About Us
	$pagecontent .= GenSitemapFromPageID(603, '0.9'); //Contact/DMCA
	$pagecontent .= GenSitemapFromPageID(604, '0.5'); //Using Magnet Links
	$pagecontent .= GenSitemapFromPageID(605, '0.5'); //Using IPFS
	$pagecontent .= GenSitemapFromPageID(606, '0.6'); //Stats (no html)
	$pagecontent .= GenSitemapFromPageID(607, '0.8'); //Site News
	$pagecontent .= '<!-- Navigation -->' . "\n";
	$pagecontent .= GenSitemapFromPageID(1, '0.8'); //1 = Playstation (official)
	$pagecontent .= GenSitemapFromPageID(2, '0.7'); //2 = Playstation (unofficial)
	$pagecontent .= GenSitemapFromPageID(3, '0.7'); //3 = Playstation (tools + utils)
	$pagecontent .= GenSitemapFromPageID(4, '0.8'); //4 = Playstation 2 (official)
	$pagecontent .= GenSitemapFromPageID(5, '0.7'); //5 = Playstation 2 (unofficial)
	$pagecontent .= GenSitemapFromPageID(6, '0.7'); //6 = Playstation 2 (tools + utils)
	$pagecontent .= GenSitemapFromPageID(7, '0.8'); //7 = Windows (Games)
	$pagecontent .= GenSitemapFromPageID(8, '0.7'); //8 = Windows (Software)
	$pagecontent .= GenSitemapFromPageID(9, '0.8'); //9 = MS-DOS (Games)
	$pagecontent .= GenSitemapFromPageID(10, '0.7'); //10 = MS-DOS (Software)
	$pagecontent .= GenSitemapFromPageID(11, '0.8'); //11 = Nintendo Entertainment System (official)
	$pagecontent .= GenSitemapFromPageID(12, '0.7'); //12 = Nintendo Entertainment System (unofficial)
	$pagecontent .= GenSitemapFromPageID(13, '0.8'); //13 = Super Nintendo Entertainment System (official)
	$pagecontent .= GenSitemapFromPageID(14, '0.7'); //14 = Super Nintendo Entertainment System (unofficial)
	$pagecontent .= GenSitemapFromPageID(15, '0.8'); //15 = Nintendo 64 (official)
	$pagecontent .= GenSitemapFromPageID(16, '0.7'); //16 = Nintendo 64 (unofficial)
	$pagecontent .= GenSitemapFromPageID(17, '0.8'); //17 = Nintendo GameCube (official)
	$pagecontent .= GenSitemapFromPageID(18, '0.7'); //18 = Nintendo GameCube (unofficial)
	$pagecontent .= GenSitemapFromPageID(19, '0.7'); //19 = Nintendo GameCube (tools + utils)
	$pagecontent .= GenSitemapFromPageID(20, '0.8'); //20 = Sega Master System (official)
	$pagecontent .= GenSitemapFromPageID(21, '0.7'); //21 = Sega Master System (unofficial)
	$pagecontent .= GenSitemapFromPageID(22, '0.8'); //22 = Sega Genesis/Megadrive (official)
	$pagecontent .= GenSitemapFromPageID(23, '0.7'); //23 = Sega Genesis/Megadrive (unofficial)
	$pagecontent .= GenSitemapFromPageID(24, '0.7'); //24 = Sega Mega CD (official)
	$pagecontent .= GenSitemapFromPageID(25, '0.7'); //25 = Sega 32X (official)
	$pagecontent .= GenSitemapFromPageID(26, '0.8'); //26 = Sega Saturn (official)
	$pagecontent .= GenSitemapFromPageID(27, '0.8'); //27 = Sega Dreamcast (official)
	$pagecontent .= GenSitemapFromPageID(28, '0.7'); //28 = Sega Dreamcast (unofficial)
	$pagecontent .= GenSitemapFromPageID(29, '0.7'); //29 = Sega Dreamcast (tools + utils)
	$pagecontent .= GenSitemapFromPageID(30, '0.8'); //30 = Xbox (official)
	$pagecontent .= GenSitemapFromPageID(31, '0.7'); //31 = Xbox (unofficial)
	$pagecontent .= GenSitemapFromPageID(32, '0.7'); //32 = Xbox (tools + utils)
	$pagecontent .= GenSitemapFromPageID(33, '0.8'); //33 = Nintendo Gameboy (Official)
	$pagecontent .= GenSitemapFromPageID(34, '0.7'); //34 = Nintendo Gameboy (Tools + Utils)
	$pagecontent .= GenSitemapFromPageID(35, '0.8'); //35 = Nintendo Gameboy Color (Official)
	$pagecontent .= GenSitemapFromPageID(36, '0.7'); //36 = Nintendo Gameboy Color (Tools + Utils)
	$pagecontent .= GenSitemapFromPageID(37, '0.8'); //37 = Nintendo Gameboy Advance (Official)
	$pagecontent .= GenSitemapFromPageID(38, '0.7'); //38 = Nintendo Gameboy Advance (Unofficial)
	$pagecontent .= GenSitemapFromPageID(39, '0.7'); //39 = Nintendo Gameboy Advance (Tools + Utils)
	$pagecontent .= GenSitemapFromPageID(40, '0.8'); //40 = Nintendo DS (Official)
	$pagecontent .= GenSitemapFromPageID(41, '0.7'); //41 = Nintendo DS (Unofficial)
	$pagecontent .= GenSitemapFromPageID(42, '0.7'); //42 = Nintendo DS (Tools + Utils)
	$pagecontent .= GenSitemapFromPageID(43, '0.8'); //43 = Sony PSP (Official)
	$pagecontent .= GenSitemapFromPageID(44, '0.7'); //44 = Sony PSP (Unofficial)
	$pagecontent .= GenSitemapFromPageID(45, '0.7'); //45 = Sony PSP (Tools + Utils)
	$pagecontent .= GenSitemapFromPageID(46, '0.8'); //46 = Commodore 64 (Games)
	$pagecontent .= GenSitemapFromPageID(47, '0.7'); //47 = Commodore 64 (Software)
	$pagecontent .= GenSitemapFromPageID(48, '0.8'); //48 = Sega Game Gear (official)
	$pagecontent .= GenSitemapFromPageID(49, '0.7'); //49 = Sega Game Gear (unofficial)
	$pagecontent .= GenSitemapFromPageID(50, '0.8'); //50 = PC Engine / TurboGrafx-16 (official)
	$pagecontent .= GenSitemapFromPageID(51, '0.7'); //51 = PC Engine / TurboGrafx-16 (unofficial)
	$pagecontent .= GenSitemapFromPageID(52, '0.7'); //52 = PC Engine / TurboGrafx-16 (CD-ROM)
	$pagecontent .= GenSitemapFromPageID(53, '0.7'); //23 = 53 = PC Engine / TurboGrafx-16 (Tools + Utils)
	$pagecontent .= '<!-- Game Pages -->' . "\n";
	$pagecontent .= GenSitemapFromGames();
	$pagecontent .= '<!-- Submissions -->' . "\n";
	$pagecontent .= GenSitemapFromSubmissions();
	$pagecontent .= '<!-- Comments -->' . "\n";
	$pagecontent .= GenSitemapFromComments();
	$pagecontent .= '</urlset>'."\n";
	file_put_contents('./sitemap.xml', $pagecontent);
	echo $pagecontent;	
?>