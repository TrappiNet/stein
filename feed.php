<?php
session_start();
require('request.php');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Stein</title>
    <link rel="stylesheet" type="text/css" href="stein.css" />
    <script type="text/javascript">
function remove_extern(){
	// Removes the external hrefs from the like button so that users can like without dropping the page.
	var likes = document.getElementsByClassName("like");
	for(var i=0;i<likes.length;i++){
		likes[i].setAttribute("href","#");
	}
}

window.onload=remove_extern;

var active_like;

function like(id){
	req = new XMLHttpRequest();
	if(!req){
		alert('Error while attempting to create XMLHttpRequest');
		return;
	}
	active_like = id;
	req.onreadystatechange = like_response;
	req.open('GET','./like.php?id='+id,true);
	req.send(null);
}

function like_response(){
	if(req.readyState === XMLHttpRequest.DONE){
		if(req.status === 200){
			var elem = document.getElementById("like"+active_like);
			if(elem.innerHTML === "Like"){
				elem.innerHTML = "Unlike";
			}else{
				elem.innerHTML = "Like";
			}
		}else{
			alert(req.status+' returned by XMLHttpRequest: '+req.responseText);
		}
	}
}

function follow(target){
	req = new XMLHttpRequest();
	if(!req){
		alert('Error while attempting to create XMLHttpRequest');
		return;
	}
	req.onreadystatechange = follow_response;
	req.open('GET','./follow.php?target='+target,true);
	req.send(null);
}

function follow_response(){
	if(req.readyState === XMLHttpRequest.DONE){
		if(req.status === 200){
			var elem = document.getElementById("follow");
			if(elem.innerHTML === "Follow"){
				elem.innerHTML = "Unfollow";
			}else{
				elem.innerHTML = "Follow";
			}
		}else{
			alert(req.status+' returned by XMLHttpRequest: '+req.responseText);
		}
	}
}
    </script>
  </head>
  <body>
    <?php include("header.php"); ?>
    <div style="width:70%;overflow:hidden;">
      <div style="float:left;">
	<?php
if(isset($_GET['user']) && getuid($_GET['user']) !== null){
	$uid = getuid($_GET['user']);
	echo '<a href="ask.php?target='.$uid.'&amp;redirect=feed.php%3Fuser%3D'.$_GET['user'].'">Ask</a>';
	if(isset($_SESSION['uid']) && $_SESSION['uid'] !== $uid){
		echo ' · <a class="follow" id="follow" onclick="follow('.$_SESSION['uid'].','.$uid.'" href="follow.php?target='.$uid.'&redirect=feed.php%3Fuser%3D'.$_GET['user'].'">'.(isfollowed($_SESSION['uid'],$uid) ? 'Unfollow' : 'Follow').'</a>';
	}
}
	?>
      </div>
      <div style="float:right;">
	<form method="get" action="feed.php">
	  Enter a user&apos;s name to view their feed:
	  <input type="text" name="user" />
	  <input type="submit" value="Submit" />
	</form>
      </div>
    </div>
    <hr />
    <?php
if(isset($_GET['user']) && getuid($_GET['user']) !== null){
	echo getposts($_GET['user'],'feed.php%3Fuser%3D'.$_GET['user']);
}
    ?>
  </body>
</html>
