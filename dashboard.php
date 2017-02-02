<?php
session_start();
require('request.php');
if(!isset($_SESSION['uid'])){
	// redirect them to the login page. I don't think anything else makes sense
	header('location: login.php');
	exit();
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Stein</title>
    <link rel="stylesheet" type="text/css" href="stein.css" />
  </head>
  <body>
    <?php include("header.php"); ?>
    <!-- List of either a fixed number of posts, or everything that is 'unread'. This is configurable, or alternatively we can put it in a GET parameter to this page. I like the latter better but the default is confirable. -->
  </body>
</html>
