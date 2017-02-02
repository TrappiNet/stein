<?php
if(isset($_SESSION['uid'])){
	$username = getusername($_SESSION['uid']);
}
?>
<h1>stein.</h1> 
<div style="float: left;">
  <a href="index.php">Home</a> ·
  <a href="dashboard.php">Dashboard</a> ·
  <a href="newpost.php">New Post</a> ·
  <a href="feed.php?user=<?php if(isset($username)){echo $username;} ?>">Feed</a> ·
  <a style="color: red" href="search.php">Search</a> ·
  <a href="../index.html">Neurergus</a>
</div>
<div style="float: right;">
  <?php
if(isset($_SESSION['uid'])){
	echo('Logged in as '.$username.' · <a style="color: red" href="settings.php">Settings</a> · <a href="logout.php">Logout</a>');
}else{
	echo('
  <form method="post" action="login.php">
    Username:
    <input type="text" name="username" />
    Password:
    <input type="password" name="password" />
    <input type="submit" value="Login" />
  </form>');
}
  ?>
</div>
<hr style="clear: both;" />
