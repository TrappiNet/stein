<?php
session_start();
require('request.php');

$uid = 2; //anonymous
if(isset($_SESSION['uid'])){
	$uid = $_SESSION['uid'];
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	if(!isset($_POST['content'])){
		die("Error: content not received.");
	}
	if(!isset($_POST['tags'])){
		die("Error: tags not received.");
	}
	$tags = explode(",",$_POST['tags']);
	for($i=0;$i<count($tags);$i++){
		$tags[$i] = trim($tags[$i]);
	}
	$tags = implode("\t",$tags);
	if(isset($_POST['anonymous'])){
		$uid = 2;
	}
	$replyto = NULL;
	if(isset($_POST['replyto'])){
		$replyto = $_POST['replyto'];
		if(!isvalidpost($replyto)){
			die('Error: invalid replyto '.$replyto);
		}
	}elseif($uid == 2){
		die("Error: cannot submit post without replyto as Anonymous.");
	}
	$id = submit_newpost($uid,$_POST['content'],$tags,$replyto);
	if(!isset($_POST['redirect'])){
		header('location: post.php?id='.$id);
		exit();
	}
	header('location: '.$_POST['redirect']);
	exit();
}else{
	die("Error: no data received.");
}