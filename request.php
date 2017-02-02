<?php
// For functions to interact with the database.

$db = mysqli_connect("localhost:3306","root","w2252tq","stein");

function do_query($query){
	global $db;
	$result = mysqli_query($db,$query);
	$error = mysqli_error($db);
	if($error) die($error);
	return $result;
}

function escape_string($string){
	global $db;
	return mysqli_real_escape_string($db,$string);
}

function getuid($username){
	$username = escape_string($username);
	$result = mysqli_fetch_array(do_query("SELECT id FROM users WHERE username = '$username'"),MYSQLI_ASSOC);
	if(isset($result['id'])){
		return $result['id'];
	}else{
		return null;
	}
}

function getusername($uid){
	$uid = (int) $uid;
	$result = mysqli_fetch_array(do_query("SELECT username FROM users WHERE id = $uid"),MYSQLI_ASSOC);
	return $result['username'];
}

function getposts($username,$redirect){
	$uid = getuid($username);
	$uid = (int) $uid;
	$result = do_query("SELECT id FROM posts WHERE uid = $uid ORDER BY id DESC LIMIT 20"); // todo: settings
	$out = "";
	while($post = mysqli_fetch_array($result, MYSQLI_ASSOC)){
		$out .= format_post($post['id'],$redirect).'<br /><hr />';
	}
	return $out;
}

function isvalidpost($id){
	$id = (int) $id;
	return ([] != mysqli_fetch_array(do_query("SELECT COUNT(*) FROM posts WHERE id = $id"),MYSQLI_ASSOC));
}

function format_post($id,$redirect){
	$id = (int) $id;
	$post = mysqli_fetch_array(do_query("SELECT * FROM posts WHERE id = $id"),MYSQLI_ASSOC);
	if($post == []) die('Error: no post with id '.$id.' exists. If you see this error please contact the webmaster.'); //as it means I missed a check somewhere.
	$out = '';
	if($post['replyto']){
		$out .= format_post($post['replyto'],$redirect);
	}

	$uid = $post['uid'];
	$username = getusername($uid);
	$out .= '
<img src="./avatars/'.$uid.'.png" alt="User avatar" class="avatar" />
<div class="post">
  '.$username.' · '.$post['time'].'
  <div class="post_content">'
  .parse_formatting($post['content'])
  .'</div>'
  .parse_tags($post['tags'])
  .'<div style="float:left;">';

	if(isset($_SESSION['uid'])){
		$out .= '
    <a class="like" id="like'.$id.'" onclick="like('.$id.')" href="like.php?id='.$id.'&amp;redirect='.$redirect.'">'.(isliked($_SESSION['uid'],$id) ? 'Unlike' : 'Like').'</a> ·';
	}
	$out .= '
    <a href="newpost.php?replyto='.$id.'&amp;redirect='.$redirect.'">Reblog</a> ·
    <a href="ask.php?target='.$username.'&amp;replyto='.$id.'&amp;redirect='.$redirect.'">Reply</a> ·
    <a href="post.php?id='.$id.'">Permalink</a>
  </div>
  <div style="float:right;">'
    .parse_notes($id).'
  </div>
</div>';

	return $out;
}

function get_all_notes($id, &$reblogs, &$replies, &$likes){
	$id = (int) $id;
	$reblogq = do_query("SELECT id,time FROM posts WHERE replyto = $id");
	$replyq = do_query("SELECT id,time FROM asks WHERE replyto = $id");
	$likeq = do_query("SELECT uid,time FROM likes WHERE pid = $id");
	while($post = mysqli_fetch_array($reblogq,MYSQLI_ASSOC)){
		$reblogs[$post['id']] = $post['time'];
		get_all_notes($post['id'],$reblogs,$replies,$likes);
	}
	while($post = mysqli_fetch_array($replyq,MYSQLI_ASSOC)){
		$replies[] = $post;
		$rid = (int) $post['id'];
		//we need to find all answers to this reply
		$answerq = do_query("SELECT id,time FROM posts WHERE isanswer = 1 AND replyto = $rid");
		while($answer = mysqli_fetch_array($answerq,MYSQLI_ASSOC)){
			$reblogs[] = $post;
			get_all_notes($answer['id'],$reblogs,$replies,$likes);
		}
	}
	while($post = mysqli_fetch_array($likeq,MYSQLI_ASSOC)){
		$likes[] = $post;
	}
	
}

function format_summary_reblog($id){
	// Formats a single-line summary (e.g. Tumblr's notes section).
	$id = (int) $id;
	$post = mysqli_fetch_array(do_query("SELECT uid,content FROM posts WHERE id = $id"),MYSQLI_ASSOC);
	$username = getusername($post['uid']);
	$out = '<div class="summary">';
	if($uid == 2){
		$out .= 'Anonymous';
	}else{
		$out .= '<a href="feed.php?user='.$username.'">'.$username.'</a>';
	}
	$out .= 'reblogged this and added: "';
	$out .= parse_formatting($post['content']);
//	cutoff $out
	$out .= '"</div>';
}

function parse_formatting($text){
	//todo: parse phpbb
	return str_replace("\n","<br />",$text);
}

function parse_tags($text){
	$text = '#'.implode(' · #',explode("\t",$text));
	if($text === '#') return '';
	return '<span class="tags">'.$text.'</span><br />';
}

function parse_notes($id){
	$notes = calculate_notes($id)-1;
	if($notes == 0) return '';
	return $notes.' notes';
}

function calculate_notes($id){
	// Find all likes, reblogs and replies to this post.
	$id = (int) $id;
	$likes = mysqli_fetch_array(do_query("SELECT COUNT(*) AS id FROM likes WHERE pid = $id"), MYSQLI_ASSOC)['id'];
	$replies = mysqli_fetch_array(do_query("SELECT COUNT(*) AS id FROM asks WHERE replyto = $id"), MYSQLI_ASSOC)['id'];
	$reblogs = do_query("SELECT id FROM posts WHERE replyto = $id");
	// If this post has no notes, return 0.
	$count = $likes+$replies+1;
	while($reblog = mysqli_fetch_array($reblogs, MYSQLI_ASSOC)){
		$count += calculate_notes($reblog['id']);
	}
	return $count;
}

function isliked($uid,$pid){
	$uid = (int) $uid;
	$pid = (int) $pid;
	return (mysqli_fetch_array(do_query("SELECT COUNT(*) AS id FROM likes WHERE uid=$uid AND pid=$pid"),MYSQLI_ASSOC)['id'] != 0);
}

function like($uid,$pid){
	$uid = (int) $uid;
	$pid = (int) $pid;
	if(isliked($uid,$pid)){
		// unlike
		do_query("DELETE FROM likes WHERE uid=$uid AND pid=$pid");
	}else{
		// like
		do_query("INSERT INTO likes (uid, pid) VALUES ($uid,$pid)");
	}
}

function isfollowed($uid,$target){
	$uid = (int) $uid;
	$target = (int) $target;
	return (mysqli_fetch_array(do_query("SELECT COUNT(*) AS id FROM follows WHERE uid=$uid AND target=$target"),MYSQLI_ASSOC)['id'] != 0);
}

function follow($uid,$target){
	$uid = (int) $uid;
	$target = (int) $target;
	if(isfollowed($uid,$target)){
		// unfollow
		do_query("DELETE FROM follows WHERE uid=$uid AND target=$target");
	}else{
		// follow
		do_query("INSERT INTO follows (uid, target) VALUES ($uid,$target)");
	}
}

function submit_newpost($uid,$content,$tags,$replyto){
	global $db;
	$uid = (int) $uid;
	$content = escape_string($content);
	$tags = escape_string($tags);
	$replyto = (int) $replyto;
	if($replyto == 0) $replyto = "null";
	do_query("INSERT INTO posts (uid,content,tags,replyto,isanswer) VALUES ($uid,'$content','$tags',$replyto,false)");
	return mysqli_insert_id($db);
}

function submit_ask($uid,$target,$content,$replyto){
	global $db;
	$uid = (int) $uid;
	$target = (int) getuid($target);
	$content = escape_string($content);
	$replyto = (int) $replyto;
	if($replyto == 0) $replyto = "null";
	do_query("INSERT INTO asks (uid,target,content,replyto) VALUES ($uid,$target,'$content',$replyto)");
	return mysqli_insert_id($db);
}
?>