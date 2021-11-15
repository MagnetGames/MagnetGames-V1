<?php
	include './src/globals.php';
	include './src/comments.php';
	include './src/captcha.php';
	include './src/email.php';
	$DEF_Title = 'Register';
	$DEF_Desc = 'Register an account to use on MagnetGames!';
	$pagecontent .= PageBegin();
	$TOSid = 601;
	
	//Renders the boxes to fill the registration
	function RenderRegistrationBox($preuser, $preemail, $prenotify)
	{
		$pagecontent = '<form class="SSky_Form" action="./register.php" method="post">';	
		$pagecontent .= '<b> Username (Max 16 chars): </b><input type="text" maxlength="16" name="username" value="' . $preuser . '"><br>';
		$pagecontent .= '<b> Email (Max 255 chars): </b><input type="text" maxlength="255" name="email" value="' . $preemail . '"><br><hr>';
		$pagecontent .= '<b> Password (Max 32 chars, Min 6 chars): </b><input type="password" maxlength="32" name="password"><br>';
		$pagecontent .= '<b> Confirm Password (Max 32 chars, Min 6 chars): </b><input type="password" maxlength="32" name="confirmpassword"><br>';
		if($prenotify)
		{
			$pagecontent .= '<input type="checkbox" name="notifyemail" checked><label for="notifyemail"><b>Enable Comment Email Notifications?</b></label>';
		}else
		{
			$pagecontent .= '<input type="checkbox" name="notifyemail"><label for="notifyemail"><b>Enable Comment Email Notifications?</b></label>';
		}
		$pagecontent .= '<input type="submit" value="Register!">';
		$pagecontent .= '</form>';
		return $pagecontent;
	}
	Function BadReg($ErrorMsg, $username = null, $email = null, $notifyemail = true)
	{
		GenLock();
		$pagecontent = Error($ErrorMsg);
		$pagecontent .= DrawCaptcha();
		$pagecontent .= '<hr>';
		$pagecontent .= RenderRegistrationBox($username, $email, $notifyemail);
		return $pagecontent;
	}
	
	//Logged in?
	if(isset($_SESSION['session_userid']))
	{
		$pagecontent .= Error("You're already logged in!");
	}else
	{
		$pagecontent .= '<h1><center>Register!</center></h1>';
		//Read the terms of service?
		if(isset($_POST["readtos"]))
		{
			GenLock();
			$_SESSION['session_readtos'] = true;
		}
		//Session never read the TOS.
		if(isset($_SESSION['session_readtos']) && $_SESSION['session_readtos'] == true)
		{
			if(isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirmpassword"]))
			{
				//Set all the details as html friendly
				$reg_username = htmlspecialchars(substr($_POST["username"], 0, 16)); 
				$reg_email = htmlspecialchars(substr(strtolower($_POST["email"]), 0, 255)); 
				//Don't exactly want htmlspecialchars fucking with passwords too much
				$reg_password = substr($_POST["password"], 0, 32); 
				$reg_cpassword = substr($_POST["confirmpassword"], 0, 32); 
				//Checkbox
				if(!isset($_POST["notifyemail"]))
				{
					$reg_notify = false;
				}else
				{
					$reg_notify = $_POST["notifyemail"];
				}
					
				
				//imagine having to click the registration button
				//this meme was made by spambot gang
				if($_SESSION['captcha_locked'])
				{
					$pagecontent .= BadReg('Please complete the puzzle!', $reg_username, $reg_email, $reg_notify);
				}else
				{					
					//Email notifications?
					if(isset($_POST["notifyemail"]) && $_POST["notifyemail"] == true)
					{
						$reg_notify = true;
					}else
					{
						$reg_notify = false;
					}
					//Valid username?
					if(IsValidUsername($reg_username))
					{
						//Valid... email??
						if(filter_var($reg_email, FILTER_VALIDATE_EMAIL)) 
						{
							//Check the passwords
							if($reg_password == $reg_cpassword)
							{
								//6 characters?
								if(strlen($reg_password) >= 6)
								{
									//If it's over 32 characters
									if(strlen($reg_password) > 32)
									{
										$pagecontent .= Error('Password is too long!');
										$pagecontent .= RenderNewPasswordBox($user["userid"], $_GET["key"]);
									}else //Alright I think the password shit is done
									{
										$user = GetUserFromUsername($reg_username);
										$userconflict = false;

										if($user != null)
										{
											if($user['isactivated'])
											{
												$pagecontent .= BadReg('User is already activated!', $reg_username, $reg_email, $reg_notify);
												$userconflict = true;
											}elseif($user['timecreated'] >= (time() - 300)) //Give them time to activate, 5 mins
											{
												$pagecontent .= BadReg('Username was already listed within the last 5 minutes! Give it time and check if the username ends up getting activated!', $reg_username, $reg_email, $reg_notify);
												$userconflict = true;
											}
										}
										//Wait does the email already exist?
										if(IsEmailActivated($reg_email) > 0 && $userconflict == false)
										{
	
											$pagecontent .= BadReg('Email is already activated!', $reg_username, $reg_email, $reg_notify);
											$userconflict = true;
										}
										
										//Should be ready to make the account now!
										if(!$userconflict)
										{
											$key = md5(mt_rand());
											$newpassword = password_hash($reg_password, PASSWORD_DEFAULT);
											$ipaddress = GetIP();
											//username password userid timecreated userscore lastposted isadmin isbanned userip useremail usernotify resetkey resettime isactivated
											//######## ##HASH## ###### ##Time()### 0         0          0       0        ##ip## ######### ########## ######## 0         0
											
											//Check if it already exists, if it does update it, not insert.
											if($user != null)
											{
												$userid = $user['userid'];
												$query = "UPDATE users SET username='" . PrepSQL($reg_username) . "', password='" . PrepSQL($newpassword) . "', timecreated='" . time() . "', userscore='0', lastposted='0', isadmin='0', isbanned='0', userip='" . PrepSQL($ipaddress) . "', useremail='" . PrepSQL($reg_email) . "', usernotify='" . PrepSQL($reg_notify) . "', resetkey='" . $key . "', resettime='0', isactivated='0' WHERE userid='" . PrepSQL($userid) . "'";		
											}else //Nope, isn't already there so just replace it
											{
												$userid = UserCount();
												$query = "INSERT INTO users (username, password, userid, timecreated, userscore, lastposted, isadmin, isbanned, userip, useremail, usernotify, resetkey, resettime, isactivated) VALUES ('" . PrepSQL($reg_username) . "', '" . PrepSQL($newpassword) . "', '" . $userid . "', '" . time() . "', '0', '0', '0', '0', '" . PrepSQL($ipaddress) . "', '" . PrepSQL($reg_email) . "', '" . PrepSQL($reg_notify) . "', '" . $key . "', '0', '0')";					
											}
																
											$result = mysqli_query($GLOBALS['con'], $query);
											SendEmail($reg_email, 'Please activate your account!', "<center><h1>Activate your account!</h1>Hi <b>" . $reg_username . "</b>!<br>Thanks for registering to MagnetGames!<br>In order to login to the site, you will need to have an activated account.<br><br><b><a href=" . '"https://magnet-games.com/activate.php?userid=' . $userid . '&key=' . $key . '">Click this link to activate your account.</a></b><br><br>Note: This username will become available for anyone after 5 minutes of non-activation.<br>Also the link will expire in 15 minutes.</center><br>');
											$pagecontent .= "<center>Success! An email was sent to the email address: <b>" . $reg_email . "</b>!";
											$pagecontent .= "<br>If you do not activate your account within the next 5 minutes, <i>others will able to register with your username.</i>";
											$pagecontent .= "<br>Don't forget to check the <b>spam</b> section on your email account.<br>Gmail addresses tend to take a bit longer for emails to arrive from MagnetGames.<hr>";
											$pagecontent .= '<img src="./cssimg/junk1.png"><img src="./cssimg/junk2.png"></center>';
											GenLock();
										}
										

									}
								}else
								{
									$pagecontent .= BadReg('Password needs to be at least 6 characters!!', $reg_username, $reg_email, $reg_notify);
								}
							}else //Passwords don't match
							{
								$pagecontent .= BadReg('Passwords do not match!', $reg_username, $reg_email, $reg_notify);
							}
						}else
						{
							$pagecontent .= BadReg('Invalid email address!', $reg_username, $reg_email, $reg_notify);
						}
					}else
					{
						$pagecontent .= BadReg('Bad Username! Please only use characters from 0-9, A-Z, a-z, - _', $reg_username, $reg_email, $reg_notify);
					}
				}
				//BadReg('shit username', $_POST["username"], );
			}else
			{
				$pagecontent .= DrawCaptcha();
				$pagecontent .= '<hr>';
				$pagecontent .= RenderRegistrationBox(null, null, true);
			}
		}else
		{
			$pagecontent .= file_get_contents('./src/pages/page_' . $TOSid . '.html');
			$pagecontent .= '<hr><center>Do you agree to the above?';
			$pagecontent .= '<form action="./register.php" method="get">
					<input type="hidden" name="readtos" value="1">
					<button type="submit" formmethod="post" class="Button">I agree!</button>
				</form></center>';
		}
		
	}
	$pagecontent .= GenGoBack();
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