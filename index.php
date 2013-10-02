<?php 
header("Connection: keep-alive");
require('cacheClass.php'); 


if (isset($_POST['id']) && trim($_POST['id']) !== '' && ctype_digit(trim($_POST['id']))) {
	$result = mysql_query("SELECT * FROM `data` WHERE `userid` =  ".mysql_real_escape_string(((int)trim($_POST['id'])))) or die(mysql_error());
	if(mysql_num_rows($result) > 0 ){
		redirect('Location:sig.php?id='.trim($_POST['id']));
		exit();
	} else{
		$id = trim($_POST['id']);
		$url = 'http://forum.xda-developers.com/member.php?u=' . $id;
		$cache = new SimpleCache();
		$profileContent = $cache->do_curl($url);
		$thanks = $cache->getThanks($profileContent);
		$username = $cache->getUsername($profileContent);
		if ($username == null) { exit('No such users'); }
		$rank = $cache->getRank($profileContent);
		if ($rank == '') { $rank = ''; }
		$avatar = $cache->getAvatar($profileContent);
		$lastActivity = $cache->getLastActivity($profileContent);
		if ($lastActivity == '') { $lastActivity = 'Unknown'; }
		$totalPost = $cache->getTotalPost($profileContent);
		$friends = $cache->getFriends($profileContent);
		//echo 'Username : ' . $username . ' <br> Rank :' . $rank . ' <br> ' . $avatar . ' <br> ' . $lastActivity . ' <br> Post : ' . $totalPost . ' <br> Friends : ' . $friends . ' <br>Thanks : ' . $thanks;
		//exit();
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
		imagepng($im, 'img/'.$id.'.png'); // make final
		imagedestroy($im);
		unlink('temp/cache.gif');
		unlink('temp/output.png');
		$img_src = 'img/'.$id.'.png';
		$imgbinary = fread(fopen($img_src, "r"), filesize($img_src));
		$img_str = base64_encode($imgbinary);
		mysql_query("INSERT INTO data (userid, url, imgdata, username, ipadd, date) VALUES ('".mysql_real_escape_string(trim($id))."', '".mysql_real_escape_string(trim('http://forum.xda-developers.com/member.php?u=' . trim($id)))."', '".mysql_real_escape_string(trim($img_str))."', '".mysql_real_escape_string(trim($username))."', '".$_SERVER['REMOTE_ADDR']."', '".time()."')") or die (mysql_error());
		imagedestroy($avatarImg);
		unlink('img/'.$id.'.png');
		redirect('Location:img.php?id='.trim($_POST['id']));
		exit();
	}

} else {
	?>

	<head>
	<title>XDA Signature</title>
	<link rel="stylesheet" href="css/styles.css" type="text/css" media="screen" />
	<link rel="stylesheet" type="text/css" href="css/style1.css" />
	<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="css/styleIE.css" />
	<![endif]-->
	</head>
	<html>
	<body>
	<ul class="bmenu">
	<li><a href="index.php">Home</a></li>
	<li><a href="about.php">About</a></li>
	<li><a href="disclamer.php">Disclamer</a></li>
	</ul>
	<div id="content"><br><br><br><br><br>
	<center><font size="20" color="#33B5E5"/>XDA Profile Sig [BETA]</font><form action="index.php" method="post">
	ID : <input type="text" name="id" class="inputBox" />
	<input type="submit" style="font-face: 'RobotoLight';" />
	</form>
	Paste your ID above.<br>
	eg : http://forum.xda-developers.com/member.php?u=<b><u>XXXXXXX</u></b>
	<br></div>
	</center>
	</body>
	</html> 

	<?php 
}
?>