<?php
session_start();
require('request.php');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Stein</title>
    <link rel="stylesheet" type="text/css" href="stein.css" />
  </head>
  <body>
    <?php include("header.php"); ?>
    <?php
if($_SERVER['REQUEST_METHOD'] != 'GET' || !isset($_GET['target'])){
	die("Error: no user given.");
}
if(isset($_GET['replyto'])){
	echo format_post($_GET['replyto'],'ask.php%3Freplyto%3D'.$_GET['replyto']);
}
    ?>
    <form style="width:30%" method="post" action="ask_submit.php">
      Markdown is <span style="color: red;">not yet</span> supported.<br />
      <textarea style="width:100%; height:200px;" name="content"></textarea><br />
      <?php
echo '<input type="hidden" name="target" value="'.$_GET['target'].'" />';
if(isset($_GET['redirect'])){
	echo '<input type="hidden" name="redirect" value="'.$_GET['redirect'].'" />';
}
if(isset($_GET['replyto'])){
	echo '<input type="hidden" name="replyto" value="'.$_GET['replyto'].'" />';
}
if(isset($_SESSION['uid'])){
	echo '<input type="checkbox" name="anonymous" />Post anonymously';
}else{
	echo 'Posting anonymously';
}
      ?>
      <input style="float:right;" type="submit" value="Post" />
    </form>
  </body>
</html>
