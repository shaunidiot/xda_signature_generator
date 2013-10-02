<?php 
header("Connection: keep-alive");
function keygen($amount){
	$keyset = "abcdefghijklmABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$randkey = "";
	for ($i=0; $i<$amount; $i++)
	$randkey .= substr($keyset, rand(0, strlen($keyset)-1), 1);
	return $randkey;
}
set_time_limit(0);
ini_set("display_errors", "1");
error_reporting(E_ALL); 
require('cacheClass.php'); 
if (isset($_POST['id']) && trim($_POST['id']) !== '' && ctype_digit(trim($_POST['id']))) {
	$result = mysql_query("SELECT * FROM `data` WHERE `userid` =  ".mysql_real_escape_string(((int)trim($_POST['id'])))) or die(mysql_error());
	if(mysql_num_rows($result) > 0 ){
		redirect('sig.php?id='.trim($_POST['id']));
		exit();
	} else{
	
		$id = trim($_POST['id']);
		$url = 'http://forum.xda-developers.com/member.php?u=' . $id;
		$cache = new SimpleCache();
		$profileContent = $cache->do_curl($url);

		$username = $cache->getUsername($profileContent);
		if ($username == null) { 
			exit('No such users');
		}
		
		$thanks = $cache->getThanks($profileContent);
		if ($thanks == null) { 
			$thanks = '0';
		}
		
		$rank = $cache->getRank($profileContent);
		if ($rank == null) { 
			$rank = '-rankless-'; 
		}
		
		$lastActivity = $cache->getLastActivity($profileContent);
		if ($lastActivity == null) { 
			$lastActivity = 'Unknown'; 
		}
		$avatar = $cache->getAvatar($profileContent);
		$totalPost = $cache->getTotalPost($profileContent);
		if ($totalPost == null) { 
			$totalPost = 'Unknown'; 
		}
		$friends = $cache->getFriends($profileContent);
		if ($friends == null) { 
			$friends = '0'; 
		}
		//echo 'Username : ' . $username . ' <br> Rank :' . $rank . ' <br> ' . $avatar . ' <br> ' . $lastActivity . ' <br> Post : ' . $totalPost . ' <br> Friends : ' . $friends . ' <br>Thanks : ' . $thanks;
		//exit();
		if ($avatar !== null) {
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
			if ($imgWidth > 132) {$imgWidth = 132;}
			if ($imgHeight > 132) {$imgHeight = 132;}
		} else {
			$avatarImg = imagecreatefrompng("none.png");
			$imgWidth = 132;
			$imgHeight = 132;
		}   
		$im = imagecreatefrompng ("img/cards.png");
		$color = imagecolorallocate($im, 120, 120, 120);
		$lightgrey = imagecolorallocate($im, 133, 133, 133);
		$green = imagecolorallocate($im, 75, 179, 0);
		$font = 'robotolight.ttf';
		$size = 8;
		//array imagefttext ( resource $image , float $size , float $angle , int $x , int $y , int $color , string $fontfile , string $text [, array $extrainfo ] )
		imagettftext($im, 13, 0, 10, 30, $color, $font, $username);
		imagettftext($im, 8, 0, 7, 45, $color, $font, $rank);

		imagettftext($im, 8, 0, 5, 60, $color, $font, "Total Post : ");
		imagettftext($im, 8, 0, 70, 60, $color, $font, $totalPost);
		imagettftext($im, 8, 0, 5, 75, $color, $font, "Thanks : ");
		imagettftext($im, 8, 0, 70, 75, $color, $font, $thanks);
		imagettftext($im, 8, 0, 5, 90, $color, $font, "Friends : ");
		imagettftext($im, 8, 0, 70, 90, $color, $font, $friends);
		
		imagettftext($im, 8, 0, 5, 115, $lightgrey, $font, "Last Activity : ");
		imagettftext($im, 8, 0, 5, 130, $green, $font, $lastActivity);
		
		imagecopymerge($im, $avatarImg, 100, 0, 0, 0, $imgWidth, $imgHeight, 100);
		imagepng($im, 'img/'.$id.'.png'); // make final
		imagedestroy($im);
		unlink('temp/cache.gif');
		unlink('temp/output.png');
		$img_src = 'img/'.$id.'.png';
		$imgbinary = fread(fopen($img_src, "r"), filesize($img_src));
		$img_str = base64_encode($imgbinary);
		imagedestroy($avatarImg);
		unlink('img/'.$id.'.png');
		mysql_query("INSERT INTO data (userid, url, imgdata, username, ipadd, date) VALUES ('".mysql_real_escape_string(trim($id))."', '".mysql_real_escape_string(trim('http://forum.xda-developers.com/member.php?u=' . trim($id)))."', '".mysql_real_escape_string(trim($img_str))."', '".mysql_real_escape_string(trim($username))."', '".$_SERVER['REMOTE_ADDR']."', '".time()."')") or die (mysql_error());
		redirect('img.php?id='.trim($_POST['id']));
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
	<center><font size="20" color="#33B5E5"/>XDA Profile Sig [BETA]</font><form action="cards.php" method="post">
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