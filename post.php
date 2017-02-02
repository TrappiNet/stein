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
    </script>
  </head>
  <body>
    <?php include("header.php"); ?>
    <?php
if(isset($_GET['id'])){
	echo format_post($_GET['id'],'post.php%3Fid%3D'.$_GET['id']);
}else{
	echo 'Error: no post id specified.';
}
    ?>
    <!-- now display notes. these should be in the summary-line form that Tumblr has so we can fit more on the page -->
  </body>
</html>
