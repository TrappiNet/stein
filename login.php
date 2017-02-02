<?php
session_start();
require('request.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$db = mysqli_connect('localhost:3306','root','w2252tq','stein');
	$username = mysqli_real_escape_string($db,$_POST['username']);
	$password = mysqli_real_escape_string($db,$_POST['password']);
	$result = mysqli_fetch_array(mysqli_query($db,"SELECT id,password FROM users WHERE username = '$username'"),MYSQLI_ASSOC);
	if($result == []){
		$msg = 'Unrecognized username.';
	}elseif(!password_verify($password,$result['password'])){
		$msg = 'Incorrect password.';
	}else{
		$_SESSION['uid'] = $result['id'];
		$msg = 'Redirecting you to the dashboard...';
		header('location: dashboard.php');
		exit();
	}
}else{
	$msg = '';
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
    <form method="post">
      Username:
      <input type="text" name="username" /><br />
      Password:
      <input type="password" name="password" /><br />
      <input type="submit" value="Login" />
    </form>
    <?php echo $msg; ?>
  </body>
</html>
