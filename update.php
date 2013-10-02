<?php 

require('cacheClass.php'); 
$highest_id = mysql_result(mysql_query("SELECT MAX(id) FROM data"), 0);
for ($i = $highest_id; $i > 0; $i--) {
	$result = mysql_query("SELECT * FROM `data` WHERE `id` =  ".mysql_real_escape_string(((int)trim($i)))) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$id = $row['userid'];
	$url = 'http://forum.xda-developers.com/member.php?u=' . $id;
	$cache = new SimpleCache();
	$profileContent = $cache->do_curl($url);
	$thanks = $cache->getThanks($profileContent);
	$username = $cache->getUsername($profileContent);
	$rank = $cache->getRank($profileContent);
	if ($rank == '') { $rank = ''; }
	$avatar = $cache->getAvatar($profileContent);
	$lastActivity = $cache->getLastActivity($profileContent);
	if ($lastActivity == '') { $lastActivity = 'Unknown'; }
	$totalPost = $cache->getTotalPost($profileContent);
	$friends = $cache->getFriends($profileContent);
	if ($avatar !== '') {
		$ch = curl_init($avatar);
		$fp = fopen('temp/cache.gif', 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
		$avatarImg = imagepng(imagecreatefromstring(file_get_contents("temp/cache.gif")), "temp/output.png");
		$avatarImg = imagecreatefrompng("temp/output.png");
		list($imgWidth, $imgHeight) = getimagesize("temp/output.png");
		if ($imgWidth > 60) {$imgWidth = 60;}
		if ($imgHeight > 60) {$imgHeight = 60;}
	} else {
		$avatarImg = imagecreatefrompng("none.png");
		$imgWidth = 60;
		$imgHeight = 60;
	}   
	$im = imagecreatefrompng ("bg.png");
	$color = imagecolorallocate($im, 0, 0, 0);
	$font = 'robotolight.ttf';
	$size = 8;
	imagettftext($im, 10, 0, 5, 78, $color, $font, $username);
	imagettftext($im, 14, 0, 110, 20, $color, $font, $rank);
	imagettftext($im, 10, 0, 90, 35, $color, $font, "Thanks : ");
	imagettftext($im, 10, 0, 180, 35, $color, $font, $thanks);
	imagettftext($im, 10, 0, 90, 50, $color, $font, "Last Activity : ");
	imagettftext($im, 10, 0, 180, 50, $color, $font, $lastActivity);
	imagettftext($im, 10, 0, 90, 65, $color, $font, "Total Post : ");
	imagettftext($im, 10, 0, 180, 65, $color, $font, $totalPost);
	imagettftext($im, 10, 0, 90, 78, $color, $font, "Friends : ");
	imagettftext($im, 10, 0, 180, 78, $color, $font, $friends);
	imagecopymerge($im, $avatarImg, 5, 5, 0, 0, $imgWidth, $imgHeight, 100);
	imagepng($im, 'img/'.$id.'.png'); 
	imagedestroy($im);
	unlink('temp/cache.gif');
	unlink('temp/output.png');
	$img_src = 'img/'.$id.'.png';
	$imgbinary = fread(fopen($img_src, "r"), filesize($img_src));
	$img_str = base64_encode($imgbinary);
	mysql_query("UPDATE data SET imgdata='".$img_str."' WHERE userid='".$id."'") or die (mysql_error());
	imagedestroy($avatarImg);
	unlink('img/'.$id.'.png');
}