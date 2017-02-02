<?php
session_start();
if(isset($_SESSION['uid']) && isset($_GET['target'])){
	if($_SESSION['uid'] == $_GET['target']){
		die('Error: uid and target are identical.');
	}
	require('request.php');
	follow($_SESSION['uid'],$_GET['target']);
	if(isset($_GET['redirect'])){
		header('location: '.$_GET['redirect']);
		exit();
	}
}else{
	echo 'Error: uid or target not set.';
}
?>