//Required for below
var xhttp = new XMLHttpRequest();
var canUnlock = true;
var canVote = true;
//Voting
function CUpvote(postid) 
{
	if(canVote)
	{
		xhttp.open("POST", "./cssimg/vote.php", true);
		xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhttp.send("postid=" + postid + "&votetype=upvote");
		canVote = false;
	}
}
function CDownvote(postid) 
{
	if(canVote)
	{
		xhttp.open("POST", "./cssimg/vote.php", true);
		xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhttp.send("postid=" + postid + "&votetype=downvote");
		canVote = false;
	}
}
function SUpvote(subid) 
{
	if(canVote)
	{
		xhttp.open("POST", "./cssimg/vote.php", true);
		xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhttp.send("subid=" + subid + "&votetype=upvote");
		canVote = false;
	}
}
function SDownvote(subid) 
{
	if(canVote)
	{
		xhttp.open("POST", "./cssimg/vote.php", true);
		xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhttp.send("subid=" + subid + "&votetype=downvote");
		canVote = false;
	}
}
//Selectable text
function SelectText(containerid) 
{
	if (document.selection) 
	{// IE
		var range = document.body.createTextRange();
		range.moveToElementText(document.getElementById(containerid));
		range.select();
	}else if (window.getSelection) 
	{
		var range = document.createRange();
		range.selectNode(document.getElementById(containerid));
		window.getSelection().removeAllRanges();
		window.getSelection().addRange(range);
	}
}
//Captcha stuff
function SubmitKey(key)
{
	//Stops spamming new connections until server responds
	if(canUnlock)
	{
		var key_1 = document.getElementById("key_1");
		var key_2 = document.getElementById("key_2");
		var key_3 = document.getElementById("key_3");
		var key_4 = document.getElementById("key_4");
		xhttp.open("POST", "./cssimg/captcha.php", true);
		xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhttp.send("key=" + key);
		
		//Avoids confusion when lagging
		var hint = document.getElementById("captchahint");
		hint.innerHTML = "<b>Please wait...</b><br>";
		hint.setAttribute("style", "color: #FFFF00;text-shadow: 1px 1px #000000;");
		var captchakeys = document.getElementById("captchakeys");
		captchakeys.setAttribute("style", "display: none;");
		canUnlock = false; 
		
	}
}

//Retrieve voting info
xhttp.onload = function() 
{
    if (xhttp.readyState === xhttp.DONE) 
	{
        if (xhttp.status === 200) //200 = It was successful
		{
            
			var retrieve = JSON.parse(xhttp.responseText);
			//If success and it's a comment
			if(retrieve.success && retrieve.postid)
			{
				//YES
				var cvotes = document.getElementById("cvotes_" + retrieve.postid);
				var cuv = document.getElementById("cuv_" + retrieve.postid);
				var cdv = document.getElementById("cdv_" + retrieve.postid);
				if(retrieve.newvotes > 0)
				{
					cvotes.innerHTML = "+" + retrieve.newvotes + " ";
					cvotes.className = "CommentUV";
				}else if(retrieve.newvotes < 0) 
				{
					cvotes.innerHTML = retrieve.newvotes + " ";
					cvotes.className = "CommentDV";
				}else
				{
					cvotes.innerHTML = retrieve.newvotes + " ";
					cvotes.className = null;
				}
				
				//Change the thumb images
				if(retrieve.hasvoted)
				{
					if(retrieve.votetype == 'downvote')
					{
						cuv.src = "./cssimg/thumb_gup.png";
						cdv.src = "./cssimg/thumb_down.png";
					}else //Just assume upvote
					{
						cuv.src = "./cssimg/thumb_up.png";
						cdv.src = "./cssimg/thumb_gdown.png";
					}
				}else //Unvoted
				{
					cuv.src = "./cssimg/thumb_gup.png";
					cdv.src = "./cssimg/thumb_gdown.png";
				}
				canVote = true;
			//If success and it's a submission
			}else if(retrieve.success && retrieve.subid)
			{
				//YES
				var cvotes = document.getElementById("svotes_" + retrieve.subid);
				var cuv = document.getElementById("suv_" + retrieve.subid);
				var cdv = document.getElementById("sdv_" + retrieve.subid);
				if(retrieve.newvotes > 0)
				{
					cvotes.innerHTML = "+" + retrieve.newvotes + " ";
					cvotes.className = "CommentUV";
				}else if(retrieve.newvotes < 0) 
				{
					cvotes.innerHTML = retrieve.newvotes + " ";
					cvotes.className = "CommentDV";
				}else
				{
					cvotes.innerHTML = retrieve.newvotes + " ";
					cvotes.className = null;
				}
				
				//Change the thumb images
				if(retrieve.hasvoted)
				{
					if(retrieve.votetype == 'downvote')
					{
						cuv.src = "./cssimg/thumb_gup.png";
						cdv.src = "./cssimg/thumb_down.png";
					}else //Just assume upvote
					{
						cuv.src = "./cssimg/thumb_up.png";
						cdv.src = "./cssimg/thumb_gdown.png";
					}
				}else //Unvoted
				{
					cuv.src = "./cssimg/thumb_gup.png";
					cdv.src = "./cssimg/thumb_gdown.png";
				}
				canVote = true;
			//If server responded about the captcha key
			}else if(retrieve.locked != null)
			{
				var hint = document.getElementById("captchahint");
				var lock_1 = document.getElementById("padlock_1");
				var lock_2 = document.getElementById("padlock_2");
				var lock_3 = document.getElementById("padlock_3");
				var lock_4 = document.getElementById("padlock_4");
				lock_1.src = "./cssimg/captcha.php?lock=1&anticache=" + Math.random();
				lock_2.src = "./cssimg/captcha.php?lock=2&anticache=" + Math.random();
				lock_3.src = "./cssimg/captcha.php?lock=3&anticache=" + Math.random();
				lock_4.src = "./cssimg/captcha.php?lock=4&anticache=" + Math.random();
				if(retrieve.index == 0)
				{
					if(retrieve.locked)
					{
						hint.innerHTML = "<b>Whoops! Remember to click the keys in order from the padlocks left-to-right!</b><br>";
						hint.setAttribute("style", "color: #FF0000;text-shadow: 1px 1px #000000;");
						//Network responded, reshow keys
						var captchakeys = document.getElementById("captchakeys");
						captchakeys.setAttribute("style", null);
						
					}else
					{
						hint.innerHTML = "<b>Congrats!<br>You may now proceed!</b><br>";
						hint.setAttribute("style", "color: #00FF00;text-shadow: 1px 1px #000000;");
						var captchakeys = document.getElementById("captchakeys");
						var captchaface = document.getElementById("captchaface");
						captchakeys.setAttribute("style", "display: none;");
						captchaface.setAttribute("style", null);
					}
				}else if(retrieve.index == 4)
				{
					hint.innerHTML = "Sorry, but you've ran out of attempts to complete the puzzle!<br>Please try again in 4 hours.";
					hint.setAttribute("style", "color: #FF0000;text-shadow: 1px 1px #000000;");
					var captchakeys = document.getElementById("captchakeys");
					var captchaface = document.getElementById("captchasadface");
					captchakeys.setAttribute("style", "display: none;");
					captchaface.setAttribute("style", null);
				}
				else
				{
					hint.innerHTML = "<b>Congrats!<br>Only " + (4 - retrieve.index) + " more to go.</b><br>";
					hint.setAttribute("style", "color: #00FF00;text-shadow: 1px 1px #000000;");
					//Network responded, reshow keys
					var captchakeys = document.getElementById("captchakeys");
					captchakeys.setAttribute("style", null);
				}
				canUnlock = true;
			}else
			{
				alert("Something went wrong, check if you're logged in.");
			}
        }else
		{
			alert("Connection Error!");
		}
    }
};