<?php
session_start();
if(isset($_SESSION['uid']) && isset($_GET['id'])){
	require('request.php');
	like($_SESSION['uid'],$_GET['id']);
	if(isset($_GET['redirect'])){
		header('location: '.$_GET['redirect']);
		exit();
	}
}else{
	echo 'Error: uid or id not set.';
}
?>