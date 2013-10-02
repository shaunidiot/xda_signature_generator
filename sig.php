<?php 
include "config.php";
if (isset($_GET['id']) && trim($_GET['id']) !== '' && ctype_digit(trim($_GET['id']))) {
	$result = mysql_query("SELECT * FROM `data` WHERE `userid` =  ".mysql_real_escape_string(((int)trim($_GET['id'])))) or die(mysql_error());
	if(mysql_num_rows($result) > 0 ){
	$row = mysql_fetch_assoc($result);
		$data = base64_decode($row['imgdata']);
		$im = imagecreatefromstring($data);
		if ($im !== false) {
			header('Content-Type: image/png');
			imagepng($im);
			imagedestroy($im);
		} else {
			exit();
		}
	} else{
		exit();
	}
} else {
	exit();
}