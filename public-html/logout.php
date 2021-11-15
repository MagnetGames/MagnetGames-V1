<?php
	include './src/globals.php';
	//session_destroy();
	$_SESSION['session_userid'] = null;
	$_SESSION['session_password'] = null;
	setcookie('remember_userkey', null, time() - 3600, "/");
	include './src/comments.php';
	$DEF_Title = 'Logout';
	$DEF_Desc = 'Log out of your registered MagnetGames account.';
	$pagecontent .= PageBegin();
	$pagecontent .= Alert('Successfully logged out!');
	$pagecontent .= '<meta http-equiv="Refresh" content="2; url=./' . $GLOBALS['lastpage'] . '">';
	$pagecontent .= '<center><b>Redirecting...</b></center>';
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