<?php
session_start();
require('request.php');

$uid = 2; //anonymous
if(isset($_SESSION['uid'])){
	$uid = $_SESSION['uid'];
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if(!isset($_POST['target'])){
		die('Error: target not received.');
	}elseif(getuid($_POST['target']) == NULL){
		die('Error: invalid target "'.$target.'".');
	}
	if(!isset($_POST['content'])){
		die('Error: content not received.');
	}
	if(isset($_POST['anonymous'])){
		$uid = 2;
	}
	$replyto = NULL;
	if(isset($_POST['replyto'])){
		$replyto = $_POST['replyto'];
		if(!isvalidpost($_POST['replyto'])){
			die('Error: invalid replyto "'.$replyto.'".');
		}
	}
	$id = submit_ask($uid,$_POST['target'],$_POST['content'],$replyto);
	if(!isset($_POST['redirect'])){
		header('location: post.php?id='.$id);
		exit();
	}
	header('location: '.$_POST['redirect']);
	exit();
}else{
	die('Error: no data received.');
}